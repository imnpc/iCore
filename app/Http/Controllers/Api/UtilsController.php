<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Plank\Mediable\Exceptions\MediaUploadException;
use Plank\Mediable\Facades\MediaUploader;
use Plank\Mediable\Media;
use Spatie\RouteAttributes\Attributes\Get;
use Spatie\RouteAttributes\Attributes\Post;
use Spatie\RouteAttributes\Attributes\Prefix;
use Throwable;

#[Group('工具', description: '工具管理', weight: 100)]
#[Prefix('utils')]
class UtilsController extends Controller
{
    /**
     * 文件上传
     *
     * 通用文件上传接口
     */
    #[Post('upload', middleware: (['auth:sanctum', 'forbid.banned']))]
    public function upload(Request $request): JsonResponse
    {
        // 验证请求参数
        $this->validate($request, [
            // 必须是文件
            'file' => 'required|file|max:51200',
            // 类型：0-图片, 1-文件, 2-音视频
            'type' => 'nullable|numeric|in:0,1,2',
            // 标签仅允许已知白名单，避免任意写入 avatar 等敏感标签污染数据。
            // 测试需求接口使用 tag=avatar，必须保留在白名单内。
            'tag' => ['nullable', 'string', Rule::in(['images', 'avatar', 'thumbnail', 'featured', 'gallery', 'download'])],
        ]);

        // 获取上传文件和用户信息
        $file = $request->file('file');
        $user = $request->user();

        // 确定上传类型和允许的媒体类型
        $type = getUploadType($request->type ?? 0);
        $allowedTypes = $type === 'images'
            ? [Media::TYPE_IMAGE]
            : getAllowedAggregateTypes($type);

        $media = null;

        try {
            // 使用 MediaUploader 处理文件上传
            $disk = (string) (config('mediable.default_disk') ?: config('filesystems.default', 'public'));
            $media = MediaUploader::fromSource($file)
                ->toDisk($disk)
                ->toDirectory("{$type}/".date('Y/m/d')) // 按类型和日期存储
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
                'file_url' => $media->getUrl(),
            ], __('api.messages.file_upload_success'), 200);
        } catch (MediaUploadException $e) {
            $this->cleanupUploadedMedia($media);

            return $this->fail(__('api.errors.file_upload_failed'), 422);
        } catch (Throwable $e) {
            $this->cleanupUploadedMedia($media);
            Log::error('文件上传失败', ['exception' => $e]);

            return $this->fail(__('api.errors.file_upload_failed'), 500);
        }
    }

    private function cleanupUploadedMedia(?Media $media): void
    {
        if ($media?->exists) {
            $media->delete();
        }
    }

    /**
     * 获取图形验证码
     *
     * 获取图形验证码
     *
     * @return JsonResponse
     *
     * @throws \Exception
     */
    #[Get('getCaptcha', middleware: (['throttle:60,1']))]
    public function getCaptcha(Request $request)
    {
        // 创建验证码
        $captcha = app('captcha')->create('flat', true);

        // 返回验证码信息
        return $this->success([
            // 图形验证码 key,下一步验证需要
            'captcha_key' => $captcha['key'],
            // 验证码图片 (base64 编码，需要处理)
            'captcha_img' => $captcha['img'],
        ], __('api.messages.captcha_retrieved'));
    }
}
