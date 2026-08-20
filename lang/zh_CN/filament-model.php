<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Models 模型 Label|PluralLabel
    |--------------------------------------------------------------------------
    */

    'models' => [
        'wallet_type' => '钱包类型|钱包类型',
        'admin' => '管理员|管理员',
        'user' => '用户|用户',
        'activity_log' => '操作日志|操作日志',
        'user_wallet_log' => '钱包日志|钱包日志',
        'recharge' => ' 充值记录|充值记录',
        'pay_log' => ' 支付记录|支付记录',
        'wallet' => '钱包列表|钱包列表',
        'transaction' => '交易记录|交易记录',
    ],

    /*
    |--------------------------------------------------------------------------
    | Attribute
    |--------------------------------------------------------------------------
    */

    'attributes' => [
        'wallet_type' => [
            'slug' => '英文代码',
            'description' => '说明',
            'decimal_places' => '小数位数',
            'icon' => '图标',
            'sort' => '排序',
            'is_enabled' => '是否启用',
        ],
        'admin' => [
            'password_help' => '空表示不修改密码',
        ],
        'user' => [
            'password_help' => '空表示不修改密码',
            'last_login_at' => '最近登录时间',
            'last_login_ip' => '最近登录IP',
            'activities' => '操作日志',
            'recharge' => '充值',
            'type' => '类型',
            'wallet_type' => '钱包类型',
            'money' => '金额',
            'tooltip' => '钱包充值',
            'remark' => '备注',
        ],
        'user_wallet_log' => [
            'walletType' => '钱包名称',
            'old' => '原数值',
            'add' => '变动',
            'new' => '新数值',
            'from' => '来源',
            'remark' => '备注',
            'fromUser' => '来自用户',
        ],
        'recharge' => [
            'order_sn' => '订单号',
            'user_id' => '用户ID',
            'wallet_type_id' => '钱包类型',
            'payment_type' => '支付方式',
            'platform' => '平台',
            'status' => '状态',
            'confirm_at' => '确认时间',
            'notify_log' => '通知记录',
            'remark' => '备注',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Navigation
    |--------------------------------------------------------------------------
    */

    'navigation_group' => [
        'user_permission' => [
            'name' => '权限',
        ],
        'permission' => [
            'name' => '权限',
        ],
        'user' => [
            'name' => '用户',
        ],
        'role' => [
            'name' => '权限',
        ],
        'finance' => [
            'name' => '财务',
        ],
        'wallet' => [
            'name' => '钱包',
        ],
        'setting' => [
            'name' => '设置',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | label
    |--------------------------------------------------------------------------
    */

    'label' => [
        'activity_log' => [
            'label' => '操作日志',
            'plural_label' => '操作日志',
        ],
        'user_wallet_log' => [
            'label' => '钱包日志',
            'plural_label' => '钱包日志',
        ],
        'wallet' => [
            'label' => '钱包',
            'plural_label' => '钱包',
        ],
        'wallet_type' => [
            'label' => '钱包类型',
            'plural_label' => '钱包类型',
        ],
        'recharge' => [
            'label' => '充值',
            'plural_label' => '充值',
        ],
        'pay_log' => [
            'label' => '支付记录',
            'plural_label' => '支付记录',
        ],
    ],

    /*
     * 通用字段
     *
     */
    'general' => [
        'id' => 'ID',
        'user_id' => '用户ID',
        'parent_id' => '上级ID',
        'user' => '用户',
        'name' => '名称',
        'email' => '邮箱',
        'email_verified_at' => '邮箱验证时间',
        'password' => '密码',
        'avatar' => '头像',
        'avatar_url' => '头像',
        'mobile' => '手机号',
        'status' => '状态',
        'is_enabled' => '是否启用',
        'created_at' => '创建时间',
        'updated_at' => '更新时间',
        'confirm_at' => '确认时间',
        'deleted_at' => '删除时间',
        'banned_at' => '封禁时间',
        'pay_at' => '支付时间',
        'draft' => '草稿',
        'updated' => '更新',
        'created' => '创建',
        'deleted' => '删除',
        'restored' => '恢复',
        'type' => '类型',
        'day' => '日期',
        'order_id' => '订单ID',
        'sort' => '排序',
        'order_sn' => '订单号',
        'money' => '金额',
        'remark' => '备注',
        'wallet_type_id' => '钱包类型',
        'payment_type' => '支付方式',
        'platform' => '平台',
        'notify_log' => '日志',
        'profile' => '个人资料',
        'settings' => '设置',
        'tags' => '标签',
        'tags_help' => '输入标签字符以后回车即可',
        'pay_type' => '支付类型',
        'trade_no' => '交易编号',
        'all' => '所有',
        'active' => '启用',
        'inactive' => '禁止',
    ],
    'settings' => [
        'name' => '系统设置',
        'description' => '系统设置',
        'general' => [
            'name' => '系统设置',
            'description' => '系统设置',
        ],
        'app' => [
            'title' => '网站设置',
            'description' => '设置网站信息',
            'name' => '网站名称',
            'logo' => '网站 Logo',
            'dark-logo' => '网站暗黑模式 Logo',
            'favicon' => '网站图标',
            'support' => [
                'email' => '联系邮箱',
                'phone' => '联系电话',
            ],
            'copyright' => '版权信息',
        ],
        'payment' => [
            'name' => '支付接口',
            'description' => '支付接口',
        ],
    ],
    'payment' => [
        'name' => '支付网关',
        'enabled' => '启用本支付接口',
        'channel' => [
            'alipay' => '支付宝支付',
            'wechat' => '微信支付',
        ],
        'mode' => [
            'normal' => '正常',
            'service' => '服务商',
            'sandbox' => '沙箱',
        ],
        'alipay' => [
            'app_id' => '应用 ID',
            'app_secret_cert' => '应用私钥',
            'app_public_cert_path' => '应用公钥',
            'alipay_public_cert_path' => '支付宝公钥证书',
            'alipay_root_cert_path' => '支付宝根证书',
            'return_url' => '同步回调地址',
            'notify_url' => '异步回调地址',
            'service_provider_id' => '服务商 ID',
            'mode' => '支付模式',
            'description' => '启用支付宝支付接口',
        ],
        'wechat' => [
            'mch_id' => '商户号',
            'mch_secret_key_v2' => '商户私钥',
            'mch_secret_key' => '商户密钥',
            'mch_secret_cert' => '商户私钥证书',
            'mch_public_cert_path' => '商户公钥证书',
            'notify_url' => '异步回调地址',
            'mp_app_id' => '公众号 APPID',
            'mini_app_id' => '小程序 APPID',
            'app_id' => 'APPID',
            'sub_mp_app_id' => '子公众号 APPID',
            'sub_app_id' => '子 APPID',
            'sub_mini_app_id' => '子小程序 APPID',
            'sub_mch_id' => '子商户 ID',
            'mode' => '支付模式',
            'description' => '启用微信支付接口',
        ],
    ],
    'ui' => [
        'labels' => [
            'choose_wallet_type' => '请选择要充值的钱包类型',
            'enter_amount_minimum' => '请输入数量，最低值为 1',
            'enter_remark' => '请输入备注',
            'current_balance' => '当前：:balance',
            'operation_failed' => '操作失败',
            'wallet_type_not_found' => '钱包类型不存在',
            'debit_exceeds_balance' => '扣除数量不能超过账户余额数量',
            'wallet_operation_failed' => '钱包操作执行失败，请重试',
            'operation_succeeded' => '操作成功',
            'recharge_completed' => '已成功充值 :wallet，数量：:amount',
            'debit_completed' => '已成功扣除 :wallet，数量：:amount',
            'username' => '用户名',
            'source' => '来源',
            'created_at_filter' => '创建时间',
            'start_date' => '开始日期',
            'end_date' => '结束日期',
            'enabled' => '启用',
            'disabled' => '禁用',
            'role_title_help' => '中文名称',
            'role_name_help' => '英文名使用下划线区分，例如 super_admin；编辑后不可修改',
            'wallet_slug_help' => '大写英文，创建以后不可修改',
            'wallet_decimal_places_help' => '创建以后不可修改',
            'processed_in' => '处理耗时',
            'global' => '全局',
        ],
    ],
    'enums' => [
        'labels' => [
            'from_type' => [
                'admin' => '后台',
                'default' => '默认',
                'order' => '订单',
                'recharge' => '充值',
                'withdraw' => '提现',
                'invite' => '邀请',
                'register' => '注册',
                'sign' => '签到',
                'other' => '其他',
            ],
            'pay_status' => [
                'unpaid' => '未支付',
                'paying' => '待审核',
                'paid' => '已支付',
            ],
            'pay_type' => [
                'order' => '订单',
                'recharge' => '充值',
            ],
            'payment_type' => [
                'alipay' => '支付宝',
                'wechat' => '微信',
                'balance' => '余额',
                'admin' => '管理员',
            ],
            'platform' => [
                'pc' => 'PC',
                'h5' => 'H5',
                'mp' => '公众号',
                'androidapp' => '安卓应用',
                'iosapp' => 'iOS 应用',
                'miniprogram' => '小程序',
            ],
            'withdraw_status' => [
                'pending' => '待审核',
                'success' => '已转账',
                'failed' => '驳回',
            ],
        ],
    ],
];
