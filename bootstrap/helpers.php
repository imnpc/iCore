<?php

use GuzzleHttp\Client;
use Overtrue\EasySms\EasySms;
use Plank\Mediable\Exceptions\MediaUploadException;
use Plank\Mediable\Facades\MediaUploader;
use Plank\Mediable\Media;

/**
 * 上传图片和文件
 *
 * @param $uploadedFile
 * @param array $options
 * @return false|Media
 */
function upload_images($uploadedFile, array $options = []): false|\Plank\Mediable\Media
{
    try {
        // 获取上传类型和允许的媒体类型
        $type = $options['type'] ?? 'images';
        $allowedTypes = getAllowedAggregateTypes($type);
        $directory = $options['directory'] ?? 'uploads';
        // $tag = $options['tag'] ?? 'images';

        // 基础配置
        $uploader = MediaUploader::fromSource($uploadedFile)
            ->toDirectory("{$directory}/" . date('Y/m/d')) // 按类型和日期存储
            ->setAllowedAggregateTypes($allowedTypes); // 设置允许的文件类型

        // 保留原始文件名配置
        if ($options['keep_original_name'] ?? false) {
            $uploader->useFilename(pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME));
        } else {
            $uploader->useHashForFilename('sha1'); // 使用 SHA1 哈希命名文件
        }

        // 执行上传
        $media = $uploader->upload();

        return $media;
    } catch (MediaUploadException $e) {
        // 异常处理
//        Log::error('upload_images文件上传失败: ' . $e->getMessage());
        return false;
    }
}

/**
 * 获取上传类型
 *
 * @param int|null $typeCode
 * @return string
 */
function getUploadType(?int $typeCode): string
{
    return match ($typeCode) {
        0 => 'images', // 图片
        1 => 'files',  // 文件
        2 => 'media',  // 音视频
        default => 'images', // 默认为图片
    };
}

/**
 * 获取允许的媒体类型
 *
 * @param string $type
 * @return array
 */
function getAllowedAggregateTypes(string $type): array
{
    return match ($type) {
        'images' => [Media::TYPE_IMAGE, Media::TYPE_IMAGE_VECTOR],
        'files' => [Media::TYPE_PDF, Media::TYPE_DOCUMENT, Media::TYPE_ARCHIVE, Media::TYPE_SPREADSHEET,
            Media::TYPE_PRESENTATION],
        'media' => [Media::TYPE_AUDIO, Media::TYPE_VIDEO],
        default => [Media::TYPE_IMAGE, Media::TYPE_IMAGE_VECTOR], // 默认只允许图片
    };
}

/**
 * 隐藏银行卡号
 * @param string $number
 * @param string $maskingCharacter
 * @return string
 */
function addMaskCC(string $number, string $maskingCharacter = '*'): string
{
    return substr($number, 0, 4) . str_repeat($maskingCharacter, strlen($number) - 8) . substr($number, -4);
}

/**
 * 隐藏手机号码
 * @param string $mobile
 * @param string $maskingCharacter
 * @return string
 */
function addMaskMobile(string $mobile, string $maskingCharacter = '*'): string
{
    return substr($mobile, 0, 3) . str_repeat($maskingCharacter, strlen($mobile) - 7) . substr($mobile, -4);
}

/**
 * 保留几位小数 默认 5
 * @param float $num 数字
 * @param int $precision 保留位数
 * @return float|int
 */
function number_fixed(float $num, int $precision = 2): float|int
{
    return intval($num * pow(10, $precision)) / pow(10, $precision);
}

/**
 * 获取数组内的 id
 * @param array $data 数组
 * @param string $key 提取 key
 * @return array
 */
function get_array_ids(array $data, string $key = 'id'): array
{
    $ids = [];
    foreach ($data as $item) {
        $id = $item[$key] ?? false;
        if ($id === false) {
            continue;
        }
        $ids[$id] = 0;
    }

    return array_keys($ids);
}

/**
 * 获取客户端IP(非用户服务器IP)
 * @return string
 */
function get_ip(): string
{
    $ip = 'members.3322.org/dyndns/getip';
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $ip);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $data = curl_exec($ch);

    return trim($data);
}

/**
 * 发送短信
 * @param string $mobile 手机号
 * @param int $code 验证码
 * @throws \Overtrue\EasySms\Exceptions\InvalidArgumentException
 * @throws \Overtrue\EasySms\Exceptions\NoGatewayAvailableException
 */
function send_sms(string $mobile, int $code): array
{
    $sign = config('easysms.sms_sign_name');
    $easySms = new EasySms(config('easysms'));
    // 注册
    $easySms->extend('qxt', function ($gatewayConfig) {
        // $gatewayConfig 来自配置文件里的 `gateways.mygateway`
        return new QxtGateway($gatewayConfig);
    });
    $text = '【' . $sign . '】您的验证码是：' . $code . '。请不要把验证码泄露给其他人。';

    return $easySms->send($mobile, $text);
}

/**
 * 生成唯一订单号
 * @param string $model 模型名称,首字母大写
 * @param string $field 订单号查询字段
 * @return bool|string
 */
function createNO(string $model, string $field): bool|string
{
    // 订单流水号前缀
    $prefix = date('YmdHis');
    for ($i = 0; $i < 10; $i++) {
        // 随机生成 6 位的数字
        $sn = $prefix . str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        // 查询该模型是否已经存在对应订单号
        $modelName = '\\App\\Models\\' . $model;
        $MODEL = new $modelName;
        if (!$MODEL::query()->where($field, $sn)->exists()) {
            return $sn;
        }
    }
    \Log::warning('生成单号失败-' . $modelName);

    return false;
}

/**
 * 判断是否都是中文
 * @param string $str
 * @return bool|int
 */
function isAllChinese(string $str): bool|int
{
    $len = preg_match('/^[\x{4e00}-\x{9fa5}]+$/u', $str);
    if ($len) {
        return true;
    }
    return false;
}

/**
 * 验证是否是url
 * @param string $url url
 * @return boolean        是否是url
 */
function is_url(string $url): bool
{
    if (filter_var($url, FILTER_VALIDATE_URL)) {
        return true;
    } else {
        return false;
    }
}

/**
 * 格式化数字
 * @param int $number
 * @return int|string
 */
function float_number(int $number): int|string
{
    $length = strlen($number);  // 数字长度
    if ($length > 8) { // 亿单位
        $str = substr_replace(floor($number * 0.0000001), '.', -1, 0) . "亿";
    } elseif ($length > 4) { // 万单位
        // 截取前俩为
        $str = floor($number * 0.001) * 0.1 . "万";
    } else {
        return $number;
    }

    return $str;
}

/**
 * 二维数组根据某个字段排序
 * @param array $array 要排序的数组
 * @param string $keys 要排序的键字段
 * @param string $sort 排序类型  SORT_ASC     SORT_DESC
 * @return array 排序后的数组
 */
function arraySort(array $array, string $keys, string $sort = SORT_DESC): array
{
    $keysValue = [];
    foreach ($array as $k => $v) {
        $keysValue[$k] = $v[$keys];
    }
    array_multisort($keysValue, $sort, $array);

    return $array;
}

/**
 * 二分查找法
 * @param int $num 数量
 * @param array $filter 对应集合
 * @return array
 */
function priceSearch(int $num, array $filter)
{
    if (count($filter) == 1) {
        return $filter;
    }
    $half = floor(count($filter) / 2); // 取出中间数

    // 判断数量在哪个区间
    if ($num < $filter[$half]['number']) {
        $filter = array_slice($filter, 0, $half);
    } else {
        $filter = array_slice($filter, $half, count($filter));
    }
    //print_r($filter);
    // 继续递归直到只剩一个元素
    if (count($filter) > 1) {
        $filter = priceSearch($num, $filter);
    }

    return $filter;
}

/**
 * 格式化文件大小
 * @param int $filesize 字节
 * @return string
 */
function getFileSize(int $filesize): string
{
    if ($filesize >= 1073741824) {
        $filesize = round($filesize / 1073741824 * 100) / 100 . ' GB';
    } elseif ($filesize >= 1048576) {
        $filesize = round($filesize / 1048576 * 100) / 100 . ' MB';
    } elseif ($filesize >= 1024) {
        $filesize = round($filesize / 1024 * 100) / 100 . ' KB';
    } else {
        $filesize = $filesize . ' 字节';
    }

    return $filesize;
}

/**
 * 替换图片域名
 * @param $string
 * @return string|string[]
 */
function replace_domain($string): array|string
{
    if (config('filesystems.default') == 'public') {
        $domain = config('app.url') . '/storage/';
        return str_ireplace($domain, '', $string);
    } elseif (config('filesystems.default') == 'oss') {
        $domain = config('filesystems.disks.oss.url') . '/';
        return str_ireplace($domain, '', $string);
    }
}

/**
 * 快递物流查询
 * @param $express_sn
 * @param $express_code
 * @return mixed|string
 * @throws \GuzzleHttp\Exception\GuzzleException
 */
function express($express_sn, $express_code)
{
    $host = "http://wdexpress.market.alicloudapi.com"; // API 访问链接
    $path = "/gxali"; // API 访问后缀
    $appcode = config('app.aliyun_appcode');
    $querys = "n={$express_sn}&t={$express_code}"; // 参数写在这里
    $url = $host . $path . "?" . $querys;

    $client = new Client();
    $response = $client->request('GET', $url, [
        'verify'  => false,
        'headers' => [
            'Authorization' => "APPCODE " . $appcode,
        ]
    ]);

    $httpCode = $response->getStatusCode();
    switch ($httpCode) {
        case 200:
            $res = json_decode($response->getBody(), true);
            return $res;
        default:
            return '查询异常';
    }
}

/**
 * 身份证核验
 * @param string $name 姓名
 * @param string $number 身份证号
 * @return bool|string
 */
function eidCheck(string $name, string $number): bool|string
{
    // 阿里云云市场接口 https://market.aliyun.com/products/57000002/cmapi026109.html
    $appcode = config('app.aliyun_appcode');
    $host = "https://eid.shumaidata.com";
    $path = "/eid/check";
    $method = "POST";
    $headers = array();
    array_push($headers, "Authorization:APPCODE " . $appcode);
    $querys = "idcard=" . $number . "&name=" . urlencode($name);
    $url = $host . $path . "?" . $querys;

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curl, CURLOPT_FAILONERROR, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HEADER, false);
    if (1 == strpos("$" . $host, "https://")) {
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    }
    $result = curl_exec($curl);

    return $result;
}
