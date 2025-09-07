<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\Request;
use Plank\Mediable\Exceptions\MediaUploadException;
use Plank\Mediable\Facades\MediaUploader;
use Spatie\RouteAttributes\Attributes\Get;
use Spatie\RouteAttributes\Attributes\Post;
use Spatie\RouteAttributes\Attributes\Prefix;

#[Group('工具', description: '工具管理', weight: 100)]
#[Prefix('utils')]
class UtilsController extends Controller
{
    /**
     * 文件上传
     *
     * 通用文件上传接口
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    #[Post('upload', middleware: (['auth:sanctum']))]
    public function upload(Request $request)
    {
        // 验证请求参数
        $this->validate($request, [
            // 必须是文件
            'file' => 'required|file',
            // 类型：0-图片, 1-文件, 2-音视频
            'type' => 'numeric|in:0,1,2',
            // 标签 例如 'avatar','thumbnail','featured','gallery' or 'download'
            'tag'  => 'string|min:1|max:48',
        ]);

        // 获取上传文件和用户信息
        $file = $request->file('file');
        $user = $request->user();

        // 确定上传类型和允许的媒体类型
        $type = getUploadType($request->type ?? 0);
        $allowedTypes = getAllowedAggregateTypes($type);

        try {
            // 使用 MediaUploader 处理文件上传
            $media = MediaUploader::fromSource($file)
                ->toDirectory("{$type}/" . date('Y/m/d')) // 按类型和日期存储
                ->useHashForFilename('sha1') // 使用 SHA1 哈希命名文件
                ->setAllowedAggregateTypes($allowedTypes) // 设置允许的文件类型
                ->upload();

            // 将媒体与用户关联
            $user->attachMedia($media, $request->tag ?? 'images');

            // 返回文件路径和 URL
            return $this->success([
                // 文件路径
                'file_path' => $media->getDiskPath(),
                // 文件网址
                'file_url'  => $media->getUrl(),
            ], '上传成功', 200);
        } catch (MediaUploadException $e) {
            // 捕获异常并返回错误响应 $e->getMessage()
//            Log::error('upload文件上传失败: ' . $e->getMessage());
            return $this->fail('文件上传失败,请确认文件格式正确!', 500);
        }
    }

    /**
     * 获取图形验证码
     *
     * 获取图形验证码
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    #[Get('getCaptcha')]
    public function getCaptcha(Request $request)
    {
        // 创建验证码
        $captcha = app('captcha')->create('flat', true);

        // 返回验证码信息
        return $this->success([
            // 图形验证码 key,下一步验证需要
            'captcha_key' => $captcha['key'],
            // 验证码图片 (base64 编码，需要处理)
            'captcha_img' => $captcha['img']
        ], '验证码获取成功!');
    }

    /**
     * 获取指定用户 token
     *
     * 测试接口，上线以后删除本接口
     *
     * @param Request $request
     * @return array|\Illuminate\Http\JsonResponse
     */
    #[Get('getUserToken')]
    public function getUserToken(Request $request)
    {
        $this->validate($request, [
            // 用户 ID
            'user_id' => 'required|numeric',
        ]);

        $user = User::find($request->user_id);

        // 返回用户登录 access_token
        return $this->success([
            // token类型
            'token_type'   => "Bearer",
            // 用户 access_token
            'access_token' => $user->createToken('api')->plainTextToken,
            // 用户 ID
            'user_id'      => $request->user_id,
        ], '注册成功', 200);
    }
}
