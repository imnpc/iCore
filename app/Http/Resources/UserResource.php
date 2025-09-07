<?php

namespace App\Http\Resources;

use Dedoc\Scramble\Attributes\SchemaName;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

#[SchemaName('UserResource')]
class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            /**
             * ID
             *
             * @example 1
             */
            'id'         => $this->id,
            /**
             *  姓名
             *
             * @example 张三
             */
            'name'       => $this->name,
            /**
             * 邮箱
             *
             * @example zhangsan@example.com
             */
            'email'      => $this->email,
            /**
             * 上级ID
             *
             * @example 1
             */
            'parent_id'      => $this->parent_id,
            /**
             * 邀请码
             *
             * @example 123456
             */
            'invite_code'      => $this->invite_code,
            /**
             * 头像
             *
             * avatar.jpg
             */
            'avatar'     => $this->avatar,
            /**
             * 头像URL
             *
             * @example https://example.com/avatar.jpg
             */
            'avatar_url'     => $this->avatar_url,
            /**
             * 创建时间
             *
             * @example 2023-10-01 00:00:00
             */
            'created_at' => (string)$this->created_at,
            /**
             * 更新时间
             *
             * @example 2023-10-01 00:00:00
             */
            'updated_at' => (string)$this->updated_at,
            /**
             * 钱包
             */
            'wallets' => WalletResource::collection($this->whenLoaded('wallets')),
        ];
    }
}
