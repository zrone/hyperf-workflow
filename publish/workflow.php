<?php

declare(strict_types=1);
/**
 * application for lyky.
 *
 * @author   zrone<xujining2008@126.com>
 */
return [
    [
        'name' => 'workflow_name',
        // 状态
        'places' => [
            'draft',
            'reviewed',
            'rejected',
            'published',
        ],
        // 工作流
        'transitions' => [
            ['name' => 'to_review', 'from' => 'draft', 'to' => 'reviewed', 'event' => null],
            ['name' => 'publish', 'from' => 'reviewed', 'to' => 'published', 'event' => null],
            ['name' => 'reject', 'from' => 'reviewed', 'to' => 'rejected', 'event' => null],
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
        // 关联 model
        'model' => Model::class,
        // model标记状态的字段名
        'property' => 'state',
        'dispatcher' => [
            'guard' => null
        ],
    ]
];
