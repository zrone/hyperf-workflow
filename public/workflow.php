<?php

declare(strict_types=1);
/**
 * application for lyky.
 *
 * @author   zrone<xujining2008@126.com>
 */
return [
    [
        // 状态
        'places' => [
            'draft',
            'reviewed',
            'rejected',
            'published',
        ],
        // 工作流
        'transitions' => [
            ['name' => 'to_review', 'from' => 'draft', 'to' => 'reviewed'],
            ['name' => 'publish', 'from' => 'reviewed', 'to' => 'published'],
            ['name' => 'reject', 'from' => 'reviewed', 'to' => 'rejected'],
        ],
        // uml 备注信息
        'attaches' => [
            'to_review' => '审核1',
            'publish' => '审核2',
            'reject' => '审核3',
        ],
        'places_metadata' => [
            'draft' => ["bg_color" => 'red', 'description' => 'draft',],
            'reviewed' => ["bg_color" => 'red', 'description' => 'draft',],
            'rejected' => ["bg_color" => 'red', 'description' => 'draft',],
            'published' => ["bg_color" => 'red', 'description' => 'draft',],
        ],
        // 工作流工作每次只允许改变到下一级状态，不允许一个工作流跳跃多个place
        'single_state' => true,
        // 标记字段的名称
        'property' => 'state',
        'dispatcher' => [
            'guard' => null,
            'leave' => null,
            'transition' => null,
            'enter' => null,
            'entered' => null,
            'completed' => null,
            'announce' => null,
        ],
    ]
];
