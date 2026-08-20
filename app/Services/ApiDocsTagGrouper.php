<?php

declare(strict_types=1);

namespace App\Services;

/**
 * API 文档标签分层分组服务。
 *
 * 将控制器上的 #[Group] 属性值映射为 "父分组 / 子分组" 层级结构。
 * 分组数据来源于 config/api-docs.php 的 tag_groups 配置。
 *
 * 新增控制器时，只需在配置文件中对应父分组下追加其 Group 名称，
 * 无需修改本类。
 */
class ApiDocsTagGrouper
{
    /**
     * 子标签 → 父分组 查找表。
     *
     * 在构造时根据配置自动生成，键为子标签（Group 名称），值为父分组名称。
     *
     * @var array<string, string>
     */
    private array $childToParent;

    /**
     * URI 关键字到父分组映射。
     *
     * @var array<string, string>
     */
    private array $uriKeywordToParent = [
        'order' => '订单',
        'orders' => '订单',
        'payment' => '订单',
        'payments' => '订单',
        'refund' => '订单',
        'refunds' => '订单',
    ];

    /**
     * 从配置构建子→父查找表。
     */
    public function __construct()
    {
        $groups = config('api-docs.tag_groups', []);

        foreach ($groups as $parent => $children) {
            foreach ((array) $children as $child) {
                $this->childToParent[$child] = (string) $parent;
            }
        }
    }

    /**
     * 生成分层标签名称。
     *
     * @param  string  $childTag  控制器 #[Group] 属性值（子标签）
     * @param  string  $uri  请求 URI（保留参数，当前未使用）
     * @return string 分层标签，如 '商品 / 商品分类'
     */
    public function makeHierarchicalTag(string $childTag, string $uri): string
    {
        $childTag = trim($childTag) !== '' ? trim($childTag) : '其他';
        $parent = $this->childToParent[$childTag] ?? $this->resolveParentByUri($uri) ?? '其他';

        return "{$parent} / {$childTag}";
    }

    /**
     * 根据 URI 推断父分组。
     */
    private function resolveParentByUri(string $uri): ?string
    {
        $segments = explode('/', trim($uri, '/'));

        foreach ($segments as $segment) {
            $normalizedSegment = strtolower((string) $segment);

            if (isset($this->uriKeywordToParent[$normalizedSegment])) {
                return $this->uriKeywordToParent[$normalizedSegment];
            }
        }

        return null;
    }
}
