# Hyperf 工作流

## 安装
```angular2html
composer require zrone/hyperf-workflow
```

安装完成后执行

```angular2html
php bin/hyperf.php vendor:publish zrone/hyperf-workflow
```

## 使用

```angular2html
public function index(WorkflowRegistry $workflowRegistry)
    {
        // 模型对象，这里仅做演示，请勿在controller直接使用model查询对象
        $wf = Workflow::query()->find(1);

        // 初始化工作流
        $registry = $workflowRegistry->create();
        // 获取当前工作流
        $wfr = $registry->get($wf, 'workflow_name');

        // 执行工作流
        if ($wfr->can($wf, 'to_review')) {
            try {
                $wfr->apply($wf, 'to_review');
                // logic

                // log
            } catch (\Exception $exception) {
                // exception
            }
        }

        return [
            'message' => 'message loading'
        ];
    }
```

### 工作流单元执行 Event 
```angular2html
class WorkflowService
{
    /**
     * @Inject
     * @var ContainerInterface $container
     */
    public $container;

    public function toReview(Event $event)
    {
        // event
        // ...
        var_dump(1);
        var_dump($event->getMetadata('label', $event->getTransition()));
    }

    public function publish(Event $event)
    {
        var_dump(2);
    }

    public function reject(Event $event)
    {
        var_dump(3);
    }
}
```

### 详细配置
```angular2html
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
            ['name' => 'to_review', 'from' => 'draft', 'to' => 'reviewed', 'event' => [\App\Services\WorkflowService::class, 'toReview']],
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
        'model' => \App\Model\Workflow::class,
        // model标记状态的字段名
        'property' => 'currentState',
        'dispatcher' => [
            'guard' => null
        ],
    ]
];
```

### 使用 UML 生成流程图

[Graphviz](https://www.graphviz.org/), 安装 Graphviz \
[PlantUML](https://plantuml.com/) 下载 PlantUML.jar，机器需要安装jdk，配置java运行环境 

windows Graphviz 安装包 https://pan.baidu.com/s/1oH7S2hIat8a7_qeQkiv78w 提取码: 5t1h 

#### Liunx & Mac
在项目根目录下 ```projectDir``` 执行命令生成uml
```angular2html
vendor/bin/workflow
```

#### Windows
在项目根目录下 ```projectDir``` 执行命令生成uml
```angular2html
vendor/bin/workflow.exe
```

生成的目录在 ```projectDir/uml``` 下