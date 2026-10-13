<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Models 模型 Label|PluralLabel
    |--------------------------------------------------------------------------
    */

    'models' => [
        'wallet_type' => '錢包類型|錢包類型',
        'admin' => '管理員|管理員',
        'user' => '使用者|使用者',
        'activity_log' => '操作日誌|操作日誌',
        'user_wallet_log' => '錢包日誌|錢包日誌',
        'recharge' => '儲值紀錄|儲值紀錄',
        'pay_log' => '支付紀錄|支付紀錄',
        'wallet' => '錢包列表|錢包列表',
        'transaction' => '交易紀錄|交易紀錄',
    ],

    /*
    |--------------------------------------------------------------------------
    | Attribute
    |--------------------------------------------------------------------------
    */

    'attributes' => [
        'wallet_type' => [
            'slug' => '英文代碼',
            'description' => '說明',
            'decimal_places' => '小數位數',
            'icon' => '圖示',
            'sort' => '排序',
            'is_enabled' => '是否啟用',
        ],
        'admin' => [
            'password_help' => '留空表示不修改密碼',
        ],
        'user' => [
            'password_help' => '留空表示不修改密碼',
            'last_login_at' => '最近登入時間',
            'last_login_ip' => '最近登入 IP',
            'activities' => '操作日誌',
            'recharge' => '儲值',
            'type' => '類型',
            'wallet_type' => '錢包類型',
            'money' => '金額',
            'tooltip' => '錢包儲值',
            'remark' => '備註',
        ],
        'user_wallet_log' => [
            'walletType' => '錢包名稱',
            'old' => '原數值',
            'add' => '異動',
            'new' => '新數值',
            'from' => '來源',
            'remark' => '備註',
            'fromUser' => '來自使用者',
        ],
        'recharge' => [
            'order_sn' => '訂單號',
            'user_id' => '使用者 ID',
            'wallet_type_id' => '錢包類型',
            'payment_type' => '支付方式',
            'platform' => '平台',
            'status' => '狀態',
            'confirm_at' => '確認時間',
            'notify_log' => '通知紀錄',
            'remark' => '備註',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Navigation
    |--------------------------------------------------------------------------
    */

    'navigation_group' => [
        'user_permission' => [
            'name' => '權限',
        ],
        'permission' => [
            'name' => '權限',
        ],
        'user' => [
            'name' => '使用者',
        ],
        'role' => [
            'name' => '權限',
        ],
        'finance' => [
            'name' => '財務',
        ],
        'wallet' => [
            'name' => '錢包',
        ],
        'setting' => [
            'name' => '設定',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | label
    |--------------------------------------------------------------------------
    */

    'label' => [
        'activity_log' => [
            'label' => '操作日誌',
            'plural_label' => '操作日誌',
        ],
        'user_wallet_log' => [
            'label' => '錢包日誌',
            'plural_label' => '錢包日誌',
        ],
        'wallet' => [
            'label' => '錢包',
            'plural_label' => '錢包',
        ],
        'wallet_type' => [
            'label' => '錢包類型',
            'plural_label' => '錢包類型',
        ],
        'recharge' => [
            'label' => '儲值',
            'plural_label' => '儲值',
        ],
        'pay_log' => [
            'label' => '支付紀錄',
            'plural_label' => '支付紀錄',
        ],
    ],

    /*
     * 通用字段
     *
     */
    'general' => [
        'id' => 'ID',
        'user_id' => '使用者 ID',
        'parent_id' => '上級 ID',
        'user' => '使用者',
        'name' => '名稱',
        'email' => '電子郵件',
        'email_verified_at' => '郵箱驗證時間',
        'password' => '密碼',
        'avatar' => '頭像',
        'avatar_url' => '頭像',
        'mobile' => '手機號碼',
        'status' => '狀態',
        'is_enabled' => '是否啟用',
        'created_at' => '建立時間',
        'updated_at' => '更新時間',
        'confirm_at' => '確認時間',
        'deleted_at' => '刪除時間',
        'banned_at' => '停用時間',
        'pay_at' => '支付時間',
        'draft' => '草稿',
        'updated' => '更新',
        'created' => '建立',
        'deleted' => '刪除',
        'restored' => '還原',
        'type' => '類型',
        'day' => '日期',
        'order_id' => '訂單 ID',
        'sort' => '排序',
        'order_sn' => '訂單號',
        'money' => '金額',
        'remark' => '備註',
        'wallet_type_id' => '錢包類型',
        'payment_type' => '支付方式',
        'platform' => '平台',
        'notify_log' => '日誌',
        'profile' => '個人資料',
        'settings' => '設定',
        'tags' => '標籤',
        'tags_help' => '輸入標籤字元後按 Enter 即可',
        'pay_type' => '支付類型',
        'trade_no' => '交易編號',
        'all' => '全部',
        'active' => '啟用',
        'inactive' => '停用',
    ],
    'settings' => [
        'name' => '系統設定',
        'description' => '系統設定',
        'general' => [
            'name' => '系統設定',
            'description' => '系統設定',
        ],
        'app' => [
            'title' => '網站設定',
            'description' => '設定網站資訊',
            'name' => '網站名稱',
            'logo' => '網站 Logo',
            'dark-logo' => '網站深色模式 Logo',
            'favicon' => '網站圖示',
            'support' => [
                'email' => '聯絡郵箱',
                'phone' => '聯絡電話',
            ],
            'copyright' => '版權資訊',
        ],
        'payment' => [
            'name' => '支付介面',
            'description' => '支付介面',
        ],
    ],
    'payment' => [
        'name' => '支付閘道',
        'enabled' => '啟用此支付介面',
        'channel' => [
            'alipay' => '支付寶支付',
            'wechat' => '微信支付',
        ],
        'mode' => [
            'normal' => '正常',
            'service' => '服務商',
            'sandbox' => '沙箱',
        ],
        'alipay' => [
            'app_id' => '應用 ID',
            'app_secret_cert' => '應用私鑰',
            'app_public_cert_path' => '應用公鑰',
            'alipay_public_cert_path' => '支付寶公鑰憑證',
            'alipay_root_cert_path' => '支付寶根憑證',
            'return_url' => '同步回調地址',
            'notify_url' => '非同步回調地址',
            'service_provider_id' => '服務商 ID',
            'mode' => '支付模式',
            'description' => '啟用支付寶支付介面',
        ],
        'wechat' => [
            'mch_id' => '商戶號',
            'mch_secret_key_v2' => '商戶私鑰',
            'mch_secret_key' => '商戶密鑰',
            'mch_secret_cert' => '商戶私鑰憑證',
            'mch_public_cert_path' => '商戶公鑰憑證',
            'notify_url' => '非同步回調地址',
            'mp_app_id' => '公眾號 APPID',
            'mini_app_id' => '小程式 APPID',
            'app_id' => 'APPID',
            'sub_mp_app_id' => '子公眾號 APPID',
            'sub_app_id' => '子 APPID',
            'sub_mini_app_id' => '子小程式 APPID',
            'sub_mch_id' => '子商戶 ID',
            'mode' => '支付模式',
            'description' => '啟用微信支付介面',
        ],
    ],
    'ui' => [
        'labels' => [
            'choose_wallet_type' => '請選擇要儲值的錢包類型',
            'enter_amount_minimum' => '請輸入數量，最低為 1',
            'enter_remark' => '請輸入備註',
            'current_balance' => '目前：:balance',
            'operation_failed' => '操作失敗',
            'wallet_type_not_found' => '錢包類型不存在',
            'debit_exceeds_balance' => '扣除數量不能超過帳戶餘額',
            'wallet_operation_failed' => '錢包操作執行失敗，請重試',
            'operation_succeeded' => '操作成功',
            'recharge_completed' => '已成功儲值 :wallet，數量：:amount',
            'debit_completed' => '已成功扣除 :wallet，數量：:amount',
            'username' => '使用者名稱',
            'source' => '來源',
            'created_at_filter' => '建立時間',
            'start_date' => '開始日期',
            'end_date' => '結束日期',
            'enabled' => '啟用',
            'disabled' => '停用',
            'role_title_help' => '中文名稱',
            'role_name_help' => '英文名使用底線區分，例如 super_admin；編輯後不可修改',
            'wallet_slug_help' => '大寫英文，建立後不可修改',
            'wallet_decimal_places_help' => '建立後不可修改',
            'processed_in' => '處理耗時',
            'global' => '全域',
        ],
    ],
    'enums' => [
        'labels' => [
            'from_type' => [
                'admin' => '後台',
                'default' => '預設',
                'order' => '訂單',
                'recharge' => '儲值',
                'withdraw' => '提現',
                'invite' => '邀請',
                'register' => '註冊',
                'sign' => '簽到',
                'other' => '其他',
            ],
            'pay_status' => [
                'unpaid' => '未支付',
                'paying' => '待審核',
                'paid' => '已支付',
            ],
            'pay_type' => [
                'order' => '訂單',
                'recharge' => '儲值',
            ],
            'payment_type' => [
                'alipay' => '支付寶',
                'wechat' => '微信',
                'balance' => '餘額',
                'admin' => '管理員',
            ],
            'platform' => [
                'pc' => 'PC',
                'h5' => 'H5',
                'mp' => '公眾號',
                'androidapp' => '安卓應用',
                'iosapp' => 'iOS 應用',
                'miniprogram' => '小程式',
            ],
            'withdraw_status' => [
                'pending' => '待審核',
                'success' => '已轉帳',
                'failed' => '駁回',
            ],
        ],
    ],
];
