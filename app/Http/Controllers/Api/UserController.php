<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Notifications\VerifyCodeEmail;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use PragmaRX\Google2FA\Google2FA;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Spatie\RouteAttributes\Attributes\Get;
use Spatie\RouteAttributes\Attributes\Post;
use Spatie\RouteAttributes\Attributes\Prefix;
use Vinkla\Hashids\Facades\Hashids;

#[Group('用户', description: '用户管理', weight: 10)]
#[Prefix('user')]
class UserController extends Controller
{
    /**
     * 登录
     *
     * 使用谷歌验证器登录的必须输入生成的6位数字
     */
    #[Post('login', middleware: (['throttle:5,1']))]
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            // 邮箱地址
            'email' => 'required|email|max:255',
            // 密码
            'password' => 'required|string|max:255',
            // 图形验证码 key(从工具->获取图形验证码获取)
            'captcha_key' => 'required|string|max:255',
            // 图片验证码
            'captcha' => 'required|string|max:255',
            // 谷歌验证器验证码(用户开启了谷歌验证器需要必填)
            'two_fa_code' => 'nullable|string|size:6',
        ]);

        // 图片验证码验证是否有效
        if (! captcha_api_check($request->captcha, $request->captcha_key, 'flat')) {
            return $this->fail(__('api.errors.captcha_invalid'), 422);
        }
        // 无状态校验，避免为 API 登录创建 web 会话（防止会话固定与多余 Cookie）。
        $user = User::where('email', $request->email)->first();
        if (! $user || ! Hash::check((string) $request->password, $user->password)) {
            return $this->fail(__('api.errors.credentials_invalid'), 401);
        }

        if ($user->isBanned()) {
            return $this->fail(__('api.errors.account_banned'), 403);
        }

        // 谷歌验证器
        if ($user->app_authentication_secret) {
            $google2fa = new Google2FA; // 谷歌验证器
            $valid = $google2fa->verifyKey($user->app_authentication_secret, $request->two_fa_code ?? '');
            if (! $valid) {
                return $this->fail(__('api.errors.two_factor_invalid'), 401);
            }
        }

        // 创建用户 access_token
        $token = $user->createToken('api')->plainTextToken;

        // 返回用户信息和 access_token
        return $this->success([
            // 用户信息
            'user' => new UserResource($user),
            // token 类型
            'token_type' => 'Bearer',
            //  用户 access_token
            'access_token' => $token,
        ], __('api.messages.login_success'), 200);
    }

    /**
     * 用户信息
     *
     * 当前登录用户详细信息
     *
     * @return User|mixed
     */
    #[Get('me', middleware: (['auth:sanctum', 'forbid.banned']))]
    public function me(Request $request): JsonResponse
    {
        // 获取用户钱包列表和余额
        $user = $request->user()->load('wallets');

        // 返回用户本人信息
        return $this->success(new UserResource($user));
    }

    /**
     * 获取邮箱验证码
     */
    #[Post('getEmailVerifyCode', middleware: (['throttle:5,1']))]
    public function getEmailVerifyCode(Request $request): JsonResponse
    {
        $request->validate([
            // 邮箱地址
            'email' => 'required|email|max:255',
            // 图形验证码 key(从工具->获取图形验证码获取)
            'captcha_key' => 'required|string|max:255',
            // 图片验证码
            'captcha' => 'required|string|max:255',
        ]);

        // 验证码验证是否有效
        if (! captcha_api_check($request->captcha, $request->captcha_key, 'flat')) {
            return $this->fail(__('api.errors.captcha_invalid'), 422);
        }

        $email = $request->email; // 邮箱地址
        // 同一邮箱 60 秒内仅允许发送一次，防止邮件轰炸与费用滥用。
        $cooldownKey = 'verificationCodeCooldown_'.$email;
        if (Cache::has($cooldownKey)) {
            return $this->fail(__('api.errors.too_many_requests'), 429);
        }
        $key = 'verificationCode_'.$email; // 缓存 key
        $expiredAt = now()->addMinutes(30); // 缓存有效期 30 分钟
        // 生成邮箱验证码
        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT); // 生成6位随机数，左侧补0
        // 发送验证码邮件（ShouldQueue，失败由队列重试承担）
        Notification::route('mail', $email)->notify(new VerifyCodeEmail($code)); // 发送邮件验证码
        Cache::put($key, ['email' => $email, 'code' => $code], $expiredAt); // 缓存验证码 30 分钟过期。
        Cache::put($cooldownKey, true, now()->addSeconds(60));
        Cache::forget('verificationCodeAttempts_'.$email);

        return $this->success('', __('api.messages.email_verification_sent'), 200);
    }

    /**
     * 邮箱注册
     */
    #[Post('emailRegister', middleware: (['throttle:5,1']))]
    public function emailRegister(Request $request): JsonResponse
    {
        $request->validate([
            // 邮箱地址 要求不存在unique:users,email
            'email' => 'required|email|max:255|unique:users,email',
            // 邮箱验证码(从用户->获取邮箱验证码获取)
            'email_verify_code' => 'required|string|size:6',
            // 密码
            'password' => 'required|string|min:8|max:72|confirmed',
            // 重复密码
            'password_confirmation' => 'required|string|min:8|max:72',
            // 邀请码
            'parent_id' => 'nullable|string|max:64',
        ]);

        $email = $request->email;
        $key = 'verificationCode_'.$email;

        $verifyData = Cache::get($key);
        if (! $verifyData || ! is_array($verifyData) || ! isset($verifyData['code'])) {
            return $this->fail(__('api.errors.verification_code_expired'), 403);
        }
        // 连续输错 10 次即销毁验证码，防止 6 位纯数字被爆破。
        $attemptsKey = 'verificationCodeAttempts_'.$email;
        if (! hash_equals((string) $verifyData['code'], (string) $request->email_verify_code)) {
            $attempts = (int) Cache::get($attemptsKey, 0) + 1;
            Cache::put($attemptsKey, $attempts, now()->addMinutes(30));
            if ($attempts >= 10) {
                Cache::forget($key);
                Cache::forget($attemptsKey);
            }

            return $this->fail(__('api.errors.verification_code_invalid'), 403);
        }

        $parent_id = 0;
        if ($request->filled('parent_id')) {
            $decode_id = Hashids::decode((string) $request->parent_id); // 解密传递的 ID
            if (empty($decode_id)) {
                return $this->fail(__('api.errors.invite_code_invalid'), 403);
            }
            // 上级必须真实存在且未被封禁，避免伪造挂靠与脏数据。
            $parent = User::query()->find($decode_id[0]);
            if (! $parent || $parent->isBanned()) {
                return $this->fail(__('api.errors.invite_code_invalid'), 403);
            }
            $parent_id = (int) $parent->getKey();
        }

        // 创建用户与邀请码必须原子完成，避免半记录。
        $user = DB::transaction(function () use ($email, $request, $parent_id) {
            $created = User::create([
                'email' => $email,
                'name' => $email,
                'password' => $request->password,
                'parent_id' => $parent_id,
            ]);
            $created->forceFill([
                'invite_code' => Hashids::encode($created->getKey()),
            ])->save();

            return $created->refresh();
        });
        // 清除验证码缓存与试错计数
        Cache::forget($key);
        Cache::forget($attemptsKey);

        // 返回成功响应
        return $this->success([
            // 用户信息
            'user' => new UserResource($user),
            // token 类型
            'token_type' => 'Bearer',
            // token
            'access_token' => $user->createToken('api')->plainTextToken,
        ], __('api.messages.login_success'), 200);
    }

    /**
     * 邮箱重置密码
     */
    #[Post('emailForgetPassword', middleware: (['throttle:5,1']))]
    public function emailForgetPassword(Request $request): JsonResponse
    {
        $request->validate([
            // 邮箱地址
            'email' => 'required|email|max:255',
            // 邮箱验证码(从用户->获取邮箱验证码获取)
            'email_verify_code' => 'required|string|size:6',
            // 密码
            'password' => 'required|string|min:8|max:72|confirmed',
            // 重复密码
            'password_confirmation' => 'required|string|min:8|max:72',
        ]);

        $email = $request->email; // 邮箱地址
        $key = 'verificationCode_'.$email; // 缓存 key

        // 验证码验证是否有效
        $verifyData = Cache::get($key);
        if (! $verifyData || ! is_array($verifyData) || ! isset($verifyData['code'])) {
            return $this->fail(__('api.errors.verification_code_expired'), 403);
        }
        $attemptsKey = 'verificationCodeAttempts_'.$email;
        if (! hash_equals((string) $verifyData['code'], (string) $request->email_verify_code)) {
            $attempts = (int) Cache::get($attemptsKey, 0) + 1;
            Cache::put($attemptsKey, $attempts, now()->addMinutes(30));
            if ($attempts >= 10) {
                Cache::forget($key);
                Cache::forget($attemptsKey);
            }

            return $this->fail(__('api.errors.verification_code_invalid'), 403);
        }

        // 查询该邮箱用户 重设密码
        $user = User::where('email', '=', $request->email)->first();
        Cache::forget($key);
        Cache::forget($attemptsKey);

        if ($user) {
            $user->update([
                'password' => $request->password, // 依赖 casts hashed，避免双重哈希
            ]);
            $user->tokens()->delete(); // 删除用户所有 token，需要重新登录
        }

        return $this->success('', __('api.messages.password_reset_success'), 200);
    }

    /**
     * 修改姓名
     */
    #[Post('updateName', middleware: (['auth:sanctum', 'forbid.banned']))]
    public function updateName(Request $request): JsonResponse
    {
        $request->validate([
            // 姓名
            'name' => 'required|string|max:255',
        ]);
        $user = $request->user();
        $user->update([
            'name' => $request->name,
        ]);

        // 返回成功响应
        return $this->success([], __('api.messages.profile_updated'));
    }

    /**
     * 更新头像
     *
     * 上传和修改头像
     */
    #[Post('updateAvatar', middleware: (['auth:sanctum', 'forbid.banned']))]
    public function updateAvatar(Request $request): JsonResponse
    {
        $user = $request->user();

        $request->validate([
            // 头像图片文件
            'avatar' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120', // 头像，限制为 5MB 图片
        ]);

        if (! $request->file('avatar')) {
            return $this->fail(__('api.errors.avatar_upload_failed'), 422);
        }
        $options['type'] = 'images';
        $options['directory'] = 'avatar';
        $options['tag'] = 'avatar';
        $media = upload_images($request->file('avatar'), $options); // 上传头像

        if (! $media) {
            return $this->fail(__('api.errors.avatar_upload_failed'), 500);
        }

        $user->attachMedia($media, $options['tag']); // 关联模型和媒体文件
        $attributes['avatar'] = $media->getDiskPath();
        $user->update($attributes);

        // 清理本人其他头像文件
        $avatars = $user->media()
            ->where('tag', '=', $options['tag'])
            ->where('id', '<>', $media->id)
            ->get();
        foreach ($avatars as $avatar) {
            $avatar->delete(); // 同时清理掉数据库记录和图片文件
        }

        // 返回头像网址
        return $this->success([
            // 头像地址
            'avatar' => $media->getUrl(),
        ], __('api.messages.avatar_upload_success'), 200);
    }

    /**
     * 修改密码
     *
     * 修改密码以后需要重新登录
     */
    #[Post('updatePassword', middleware: (['auth:sanctum', 'forbid.banned']))]
    public function updatePassword(Request $request): JsonResponse
    {
        $request->validate([
            // 旧密码
            'old_password' => 'required|string|min:8|max:72',
            // 新密码
            'password' => 'required|string|min:8|max:72|confirmed',
            // 确认密码
            'password_confirmation' => 'required|string|min:8|max:72',
        ]);
        $user = $request->user();
        // 验证旧密码
        if (! Hash::check($request->old_password, $user->password)) {
            return $this->fail(__('api.errors.old_password_invalid'), 403);
        }
        $user->update([
            'password' => $request->password, // 依赖 casts hashed，避免双重哈希
        ]);
        $user->tokens()->delete(); // 删除用户所有 token，需要重新登录

        // 返回成功响应
        return $this->success([], __('api.messages.password_updated'));
    }

    /**
     * 开始设置谷歌验证器
     *
     * 获取谷歌验证器密钥和对应二维码图片,每个用户缓存 1 小时，验证过以后才会注销缓存
     */
    #[Post('getGoogle2fa', middleware: (['auth:sanctum', 'forbid.banned']))]
    public function getGoogle2fa(Request $request): JsonResponse
    {
        $user = $request->user();
        if ($user->app_authentication_secret) {
            return $this->fail(__('api.errors.two_factor_already_set'), 403);
        }

        $google2fa = new Google2FA; // 谷歌验证器
        $key = 'two_fa_secret_'.$user->email; // 缓存key
        // 默认缓存 1 小时
        $verifyData = Cache::get($key);
        if ($verifyData) {
            $secretKey = $verifyData;
        } else {
            $secretKey = $google2fa->generateSecretKey();
            Cache::put($key, $secretKey, 3600);
        }
        // 二维码数据
        $qrCodeData = $google2fa->getQRCodeUrl(config('app.name'), $user->email, $secretKey);
        // 文件名使用不可逆哈希，避免密钥明文出现在路径与公开 URL 中。
        $fileHash = hash('sha256', $user->email.'|'.$secretKey);
        if (app()->environment('local')) {
            $path = 'google2fa/dev/'.$fileHash.'.png'; // 二维码图片名称路径
        } else {
            $path = 'google2fa/'.$fileHash.'.png'; // 二维码图片名称路径
        }
        $exists = Storage::disk(config('filesystems.default'))->exists($path); // 查询文件是否存在
        if (! $exists) {
            // 不存在生成并上传二维码图片
            $qr = QrCode::format('png')->size(300)->errorCorrection('L')->generate($qrCodeData);
            Storage::disk(config('filesystems.default'))->put($path, $qr); // 保存图片
        }

        return $this->success([
            // 谷歌验证器密钥(下一步要使用)
            'app_authentication_secret' => $secretKey,
            // 二维码图片
            'qrcode_image' => Storage::disk(config('filesystems.default'))->url($path),
        ], __('api.messages.two_factor_qr_success'), 200);
    }

    /**
     * 验证和绑定谷歌验证器
     *
     * 用户扫码以后验证谷歌验证器和生成的验证码是否匹配
     */
    #[Post('setGoogle2fa', middleware: (['auth:sanctum', 'forbid.banned']))]
    public function setGoogle2fa(Request $request): JsonResponse
    {
        $request->validate([
            // 谷歌验证器密钥
            'app_authentication_secret' => 'required|string|size:16',
            // 谷歌验证器生成的6位数验证码
            'two_fa_code' => 'required|string|size:6',
        ]);

        $user = $request->user();
        if ($user->app_authentication_secret) {
            return $this->fail(__('api.errors.two_factor_already_set'), 403);
        }
        $app_authentication_secret = (string) $request->app_authentication_secret;
        $two_fa_code = (string) $request->two_fa_code;
        // 必须与服务端下发的缓存密钥一致，防止绑定任意自生成密钥绕过下发流程。
        $key = 'two_fa_secret_'.$user->email;
        $cachedSecret = Cache::get($key);
        if (! $cachedSecret || ! hash_equals((string) $cachedSecret, $app_authentication_secret)) {
            return $this->fail(__('api.errors.two_factor_invalid'), 401);
        }
        $google2fa = new Google2FA;
        $valid = $google2fa->verifyKey($app_authentication_secret, $two_fa_code);
        if (! $valid) {
            return $this->fail(__('api.errors.two_factor_invalid'), 401);
        } else {
            $user->update([
                'app_authentication_secret' => $app_authentication_secret,
            ]);
            // 清除缓存
            Cache::forget($key);
            // 二维码图片名称（与下发时同算法，避免明文密钥路径残留）
            $fileHash = hash('sha256', $user->email.'|'.$app_authentication_secret);
            if (app()->environment('local')) {
                $path = 'google2fa/dev/'.$fileHash.'.png'; // 二维码图片名称路径
            } else {
                $path = 'google2fa/'.$fileHash.'.png'; // 二维码图片名称路径
            }
            // 设置成功以后删除图片
            Storage::disk(config('filesystems.default'))->delete($path);

            return $this->success([], __('api.messages.two_factor_bound'));
        }
    }

    /**
     * 删除谷歌验证器
     */
    #[Post('deleteGoogle2fa', middleware: (['auth:sanctum', 'forbid.banned']))]
    public function deleteGoogle2fa(Request $request): JsonResponse
    {
        $request->validate([
            // 谷歌验证器生成的6位数验证码
            'two_fa_code' => 'required|string|size:6',
        ]);
        $user = $request->user();

        if (! $user->app_authentication_secret) {
            return $this->fail(__('api.errors.two_factor_invalid'), 422);
        }
        // 验证谷歌验证器生成的6位数验证码
        $google2fa = new Google2FA; // 谷歌验证器
        $valid = $google2fa->verifyKey($user->app_authentication_secret, (string) $request->two_fa_code);
        if (! $valid) {
            return $this->fail(__('api.errors.two_factor_invalid'), 401);
        }

        $user->update([
            'app_authentication_secret' => null,
        ]);

        // 返回成功响应
        return $this->success([], __('api.messages.two_factor_removed'));
    }
}
