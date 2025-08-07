<?php

return [
    "group" => "财务",
    "transactions" => [
        "title" => "交易记录",
        "single" => "交易记录",
        "columns" => [
            "created_at" => "时间",
            "user" => "用户",
            "wallet" => "钱包",
            "amount" => "金额",
            "type" => "类型",
            "balance" => "Balance",
            "description" => "描述",
            "confirmed" => "已确认",
            "uuid" => "UUID",
            "deposit" => "增加",
            "withdraw" => "扣除",
        ],
        "filters" => [
            "accounts" => "Filter By Accounts",
        ]
    ],
    "wallets" => [
        "title" => "钱包列表",
        "columns" => [
            "created_at" => "创建时间",
            "updated_at" => "更新时间",
            "user" => "用户",
            "name" => "名称",
            "balance" => "余额",
            "credit" => "增加",
            "debit" => "扣除",
            "uuid" => "UUID",
        ],
        "action" => [
            "title" => "钱包充值",
            "current_balance" => "当前余额",
            "credit" => "增加",
            "debit" => "扣除",
            "type" => "类型",
            "amount" => "金额",
        ],
        "filters" => [
            "accounts" => "Filter By Accounts",
        ]
    ],
];
