<?php

declare(strict_types=1);
/**
 * application for lyky.
 *
 * @author   zrone<xujining2008@126.com>
 */
namespace Zrone\HyperfWorkflow;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'annotations' => [
                'scan' => [
                    'paths' => [
                        __DIR__,
                    ],
                ],
            ],
            'publish' => [
                [
                    'id' => 'config',
                    'description' => 'The config for workflow.',
                    'source' => __DIR__ . '/../publish/workflow.php',
                    'destination' => BASE_PATH . '/config/autoload/workflow.php',
                ],
            ],
        ];
    }
}
