<?php

return [
    'columns' => [
        'log_name' => [
            'label' => '类型',
        ],
        'event' => [
            'label' => '事件',
        ],
        'subject_type' => [
            'label' => '资源模型',
        ],
        'causer' => [
            'label' => '操作人员',
        ],
        'properties' => [
            'label' => '属性',
        ],
        'created_at' => [
            'label' => '操作时间',
        ],
    ],
    'filters' => [
        'created_at' => [
            'label'         => '操作时间',
            'created_from'  => '创建于 ',
            'created_until' => '创建至 ',
        ],
        'event' => [
            'label' => '事件',
        ],
    ],
];
