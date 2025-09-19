<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Notifications\VerifyCodeEmail;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
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
    #[Post('login')]
    public function login(Request $request)
    {
        $request->validate([
            // 邮箱地址
            'email'       => 'required|email|exists:users,email',
            // 密码
            'password'    => 'required',
            // 图形验证码 key(从工具->获取图形验证码获取)
            'captcha_key' => 'required|string',
            // 图片验证码
            'captcha'     => 'required|string',
            // 谷歌验证器验证码(用户开启了谷歌验证器需要必填)
            'two_fa_code' => 'string',
        ]);

        // 图片验证码验证是否有效
        if (!captcha_api_check($request->captcha, $request->captcha_key, 'flat')) {
            return $this->fail('图片验证码错误或者已使用!', 422);
        }
        // 验证用户信息
        $credentials = [
            'email'    => $request->email,
            'password' => $request->password
        ];

        if (!Auth::guard('web')->attempt($credentials)) {
            return $this->fail('帐号或者密码错误!', 401);
        }

        // 获取已认证的用户（使用同一 guard）
        $user = Auth::guard('web')->user();
        if (!$user) {
            return $this->fail('登录状态初始化失败，请重试!', 500);
        }

        // 谷歌验证器
        if ($user->app_authentication_secret) {
            $google2fa = new Google2FA(); // 谷歌验证器
            $valid = $google2fa->verifyKey($user->app_authentication_secret, $request->two_fa_code ?? '');
            if (!$valid) {
                return $this->fail('验证错误,请输入谷歌验证器生成的验证码!', 401);
            }
        }

        // 创建用户 access_token
        $token = $user->createToken('api')->plainTextToken;

        // 返回用户信息和 access_token
        return $this->success([
            // 用户信息
            'user'         => new UserResource($user),
            // token 类型
            'token_type'   => 'Bearer',
            //  用户 access_token
            'access_token' => $token
        ], '登录成功', 200);
    }

    /**
     * 用户信息
     *
     * 当前登录用户详细信息
     *
     * @param Request $request
     * @return User|mixed
     */
    #[Get('me', middleware: (['auth:sanctum']))]
    public function me(Request $request)
    {
        // 获取用户钱包列表和余额
        $user = $request->user()->load('wallets');

        // 返回用户本人信息
        return $this->success(new UserResource($user));
    }

    /**
     * 获取邮箱验证码
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    #[Post('getEmailVerifyCode')]
    public function getEmailVerifyCode(Request $request)
    {
        $request->validate([
            // 邮箱地址
            'email'       => 'required|email',
            // 图形验证码 key(从工具->获取图形验证码获取)
            'captcha_key' => 'required|string',
            // 图片验证码
            'captcha'     => 'required|string',
        ]);

        // 验证码验证是否有效
        if (!captcha_api_check($request->captcha, $request->captcha_key, 'flat')) {
            return $this->fail('图形验证码错误或者已使用!', 422);
        }

        $email = $request->email; // 邮箱地址
        $key = 'verificationCode_' . $email; // 缓存 key
        $expiredAt = now()->addMinutes(30); // 缓存有效期 30 分钟
        // 生成邮箱验证码
        $code = str_pad(random_int(1, 999999), 6, 0, STR_PAD_LEFT); // 生成6位随机数，左侧补0
        // 发送验证码邮件
        Notification::route('mail', $email)->notify(new VerifyCodeEmail($code));// 发送邮件验证码
        Cache::put($key, ['email' => $email, 'code' => $code], $expiredAt); // 缓存验证码 30 分钟过期。

        return $this->success('', '邮箱验证码发送成功!', 200);
    }

    /**
     * 邮箱注册
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    #[Post('emailRegister')]
    public function emailRegister(Request $request)
    {
        $request->validate([
            // 邮箱地址 要求不存在unique:users,email
            'email'                 => 'required|email|unique:users,email',
            // 邮箱验证码(从用户->获取邮箱验证码获取)
            'email_verify_code'     => 'required|string',
            // 密码
            'password'              => 'required|string|min:6|confirmed',
            // 重复密码
            'password_confirmation' => 'required|string|min:6',
            // 邀请码
            'parent_id'             => 'string',
        ]);

        $email = $request->email;
        $key = 'verificationCode_' . $email;

        $verifyData = Cache::get($key);
        if (!$verifyData) {
            return $this->fail('邮箱验证码已失效！', 403);
        }
        if (!hash_equals($verifyData['code'], $request->email_verify_code)) {
            return $this->fail('邮箱验证码不正确！', 403);
        }

        if ($request->parent_id) {
            $decode_id = Hashids::decode($request->parent_id);// 解密传递的 ID
            if (empty($decode_id)) {
                $data['message'] = "邀请码不正确！";
                return response()->json($data, 403);
            }
            $parent_id = $decode_id[0];// 解密后的 ID
        } else {
            $parent_id = 0;
        }

        // 创建用户
        $user = User::create([
            'email'     => $email,
            'name'      => $email,
            'password'  => bcrypt($request->password),
            'parent_id' => $parent_id,
        ]);
        $invite_code = Hashids::encode($user->id); // 个人邀请码
        $user->update([
            'invite_code' => $invite_code, // 邀请码
        ]);
        // 清除验证码缓存
        Cache::forget($key);

        // 返回成功响应
        return $this->success([
            // 用户信息
            'user'         => new UserResource($user),
            // token 类型
            'token_type'   => "Bearer",
            // token
            'access_token' => $user->createToken('api')->plainTextToken
        ], '登录成功', 200);
    }

    /**
     * 邮箱重置密码
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    #[Post('emailForgetPassword')]
    public function emailForgetPassword(Request $request)
    {
        $request->validate([
            // 邮箱地址
            'email'                 => 'required|email|exists:users,email',
            // 邮箱验证码(从用户->获取邮箱验证码获取)
            'email_verify_code'     => 'required|string',
            // 密码
            'password'              => 'required|string|min:6|confirmed',
            // 重复密码
            'password_confirmation' => 'required|string|min:6',
        ]);

        $email = $request->email; // 邮箱地址
        $key = 'verificationCode_' . $email; // 缓存 key

        // 验证码验证是否有效
        $verifyData = Cache::get($key);
        if (!$verifyData) {
            return $this->fail('邮箱验证码已失效！', 403);
        }
        if (!hash_equals($verifyData['code'], $request->email_verify_code)) {
            return $this->fail('邮箱验证码不正确！', 403);
        }

        // 查询该邮箱用户 重设密码
        $user = User::where('email', '=', $request->email)->first();
        if ($user) {
            Cache::forget($key);
            $user->update([
                'password' => bcrypt($request->password), // 更新新密码
            ]);
            $user->tokens()->delete(); // 删除用户所有 token，需要重新登录

            return $this->success('', '密码重设成功,清使用新密码登录!', 200);
        } else {
            return $this->fail('用户不存在', 403);
        }
    }

    /**
     * 修改姓名
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    #[Post('updateName', middleware: (['auth:sanctum']))]
    public function updateName(Request $request)
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
        return $this->success([], '修改用户信息成功!');
    }

    /**
     * 更新头像
     *
     * 上传和修改头像
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    #[Post('updateAvatar', middleware: (['auth:sanctum']))]
    public function updateAvatar(Request $request)
    {
        $user = $request->user();

        $request->validate([
            // 头像图片文件
            'avatar' => 'required|file', // 头像
        ]);

        if ($request->file('avatar')) {
            $options['type'] = 'images';
            $options['directory'] = 'avatar';
            $options['tag'] = 'avatar';
            $media = upload_images($request->file('avatar'), $options); // 上传头像

            if (!$media) {
                return $this->fail('头像上传失败', 500);
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
        }

        // 返回头像网址
        return $this->success([
            // 头像地址
            'avatar' => $media->getUrl(),
        ], '头像上传成功', 200);
    }

    /**
     * 修改密码
     *
     * 修改密码以后需要重新登录
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    #[Post('updatePassword', middleware: (['auth:sanctum']))]
    public function updatePassword(Request $request)
    {
        $request->validate([
            // 旧密码
            'old_password'          => 'required|string|min:6',
            // 新密码
            'password'              => 'required|string|min:6|confirmed',
            // 确认密码
            'password_confirmation' => 'required|string|min:6',
        ]);
        $user = $request->user();
        // 验证旧密码
        if (!Hash::check($request->old_password, $user->password)) {
            return $this->fail('旧密码错误!', 403);
        }
        $user->update([
            'password' => bcrypt($request->password), // 更新新密码
        ]);
        $user->tokens()->delete(); // 删除用户所有 token，需要重新登录

        // 返回成功响应
        return $this->success([], '密码修改成功!');
    }

    /**
     * 开始设置谷歌验证器
     *
     * 获取谷歌验证器密钥和对应二维码图片,每个用户缓存 1 小时，验证过以后才会注销缓存
     * @param Request $request
     * @return JsonResponse
     */
    #[Post('getGoogle2fa', middleware: (['auth:sanctum']))]
    public function getGoogle2fa(Request $request)
    {
        $user = $request->user();
        if ($user->app_authentication_secret) {
            return $this->fail('已经设置过谷歌验证器了!', 403);
        }

        $google2fa = new Google2FA(); // 谷歌验证器
        $key = 'two_fa_secret_' . $user->email; // 缓存key
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
        // 二维码图片名称
        if (config('app.env') == 'local') {
            $path = 'google2fa/dev/' . $secretKey . '.png'; // 二维码图片名称路径
        } else {
            $path = 'google2fa/' . $secretKey . '.png'; // 二维码图片名称路径
        }
        $exists = Storage::disk(config('filesystems.default'))->exists($path); // 查询文件是否存在
        if (!$exists) {
            // 不存在生成并上传二维码图片
            $qr = QrCode::format('png')->size(300)->errorCorrection('L')->generate($qrCodeData);
            Storage::disk(config('filesystems.default'))->put($path, $qr); // 保存图片
        }

        return $this->success([
            // 谷歌验证器密钥(下一步要使用)
            'app_authentication_secret' => $secretKey,
            // 二维码图片
            'qrcode_image'              => Storage::disk(config('filesystems.default'))->url($path),
        ], '获取成功,请使用谷歌验证器扫描二维码', 200);
    }

    /**
     * 验证和绑定谷歌验证器
     *
     * 用户扫码以后验证谷歌验证器和生成的验证码是否匹配
     *
     * @param Request $request
     * @return JsonResponse
     */
    #[Post('setGoogle2fa', middleware: (['auth:sanctum']))]
    public function setGoogle2fa(Request $request)
    {
        $request->validate([
            // 谷歌验证器密钥
            'app_authentication_secret' => 'required|string',
            // 谷歌验证器生成的6位数验证码
            'two_fa_code'               => 'required|string',
        ]);

        $user = $request->user();
        if ($user->app_authentication_secret) {
            return $this->fail('已经设置过谷歌验证器了!', 403);
        }
        $app_authentication_secret = $request->app_authentication_secret;
        $two_fa_code = $request->two_fa_code;
        $google2fa = new Google2FA();
        $valid = $google2fa->verifyKey($app_authentication_secret, $two_fa_code);
        if (!$valid) {
            return $this->fail('验证错误,请输入谷歌验证器生成的验证码!', 401);
        } else {
            $user->update([
                'app_authentication_secret' => $app_authentication_secret,
            ]);
            // 清除缓存
            $key = 'two_fa_secret_' . $user->email;
            Cache::forget($key);
            // 二维码图片名称
            if (config('app.env') == 'local') {
                $path = 'google2fa/dev/' . $app_authentication_secret . '.png'; // 二维码图片名称路径
            } else {
                $path = 'google2fa/' . $app_authentication_secret . '.png'; // 二维码图片名称路径
            }
            // 设置成功以后删除图片
            Storage::disk(config('filesystems.default'))->delete($path);

            return $this->success([], '谷歌验证器绑定成功!');
        }
    }

    /**
     * 删除谷歌验证器
     *
     * @param Request $request
     * @return JsonResponse
     */
    #[Post('deleteGoogle2fa', middleware: (['auth:sanctum']))]
    public function deleteGoogle2fa(Request $request)
    {
        $request->validate([
            // 谷歌验证器生成的6位数验证码
            'two_fa_code' => 'required|string',
        ]);
        $user = $request->user();

        // 验证谷歌验证器生成的6位数验证码
        $google2fa = new Google2FA(); // 谷歌验证器
        $valid = $google2fa->verifyKey(Auth::user()->app_authentication_secret, $request->two_fa_code);
        if (!$valid) {
            return $this->fail('验证错误,请输入谷歌验证器生成的验证码!', 401);
        }

        $user->update([
            'app_authentication_secret' => null,
        ]);

        // 返回成功响应
        return $this->success([], '谷歌验证器删除成功!');
    }
}
