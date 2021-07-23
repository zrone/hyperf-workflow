#!/usr/bin/env php
<?php

use Zrone\Component\Workflow\DefinitionBuilder;
use Zrone\Component\Workflow\Dumper\PlantUmlDumper;use Zrone\Component\Workflow\Metadata\InMemoryMetadataStore;use Zrone\Component\Workflow\Transition;

define('BASE_PATH', __DIR__ . '/../../../');
include BASE_PATH . 'vendor/autoload.php';

if (is_file(BASE_PATH . 'config/autoload/workflow.php')) {
    $config = include (BASE_PATH . 'config/autoload/workflow.php');

    $workflowName = trim($argv[1]);

    foreach ($config as $workflow) {
        if ($workflowName != $workflow['name']) continue;
        $definitionBuilder = new DefinitionBuilder();

        list($storage, $prepareTrans) = buildTransition($workflow['transitions'], $workflow['attaches']);

        $definition = $definitionBuilder->addPlaces($workflow['places'])
            ->addTransitions($prepareTrans)
            ->setMetadataStore(buildMetadataStore($workflow['places_metadata'], $storage))
            ->build();

        $dumper = new PlantUmlDumper("arrow");
        echo $dumper->dump($definition);
        break;
    }
} else {
    echo '配置文件不存在';
}

/**
 * 注册工作流
 *
 * @param array|null $transitions
 * @param array $attach
 * @return array
 */
function buildTransition(?array $transitions, array $attach = []): array
{
    $storage = new \SplObjectStorage();
    $prepareTrans = [];

    foreach ($transitions as $transConfig) {
        $transition = new Transition($transConfig['name'], $transConfig['from'], $transConfig['to'], $transConfig['event']);
        isset($attach[$transConfig['name']]) && $storage->attach($transition, ['label' => $attach[$transConfig['name']]]);

        $prepareTrans[] = $transition;
    }
    return [$storage, $prepareTrans];
}

/**
 * 添加 uml 样式控制
 *
 * @param array|null $metadata
 * @param \SplObjectStorage $storage
 * @return InMemoryMetadataStore
 */
function buildMetadataStore(?array $metadata, \SplObjectStorage $storage)
{
    return new InMemoryMetadataStore([], $metadata, $storage);
}