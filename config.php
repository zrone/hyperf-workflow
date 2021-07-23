#!/usr/bin/env php
<?php

use Zrone\Component\Workflow\DefinitionBuilder;
use Zrone\Component\Workflow\Dumper\PlantUmlDumper;use Zrone\Component\Workflow\Metadata\InMemoryMetadataStore;use Zrone\Component\Workflow\Transition;

define('BASE_PATH', __DIR__ . '/../../../');
include BASE_PATH . 'vendor/autoload.php';

if (is_file(BASE_PATH . 'config/autoload/workflow.php')) {
    $config = include (BASE_PATH . 'config/autoload/workflow.php');

    $workflows = [];
    foreach($config as $item){
        $workflows[] = ["name" => $item['name']];
    }
    echo json_encode($workflows);
} else {
    echo json_encode([]);
}