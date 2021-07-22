<?php


namespace Zrone\HyperfWorkflow;

use Hyperf\Contract\ConfigInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Workflow\DefinitionBuilder;
use Symfony\Component\Workflow\Metadata\InMemoryMetadataStore;
use Symfony\Component\Workflow\Registry;
use Symfony\Component\Workflow\SupportStrategy\InstanceOfSupportStrategy;
use Symfony\Component\Workflow\Transition;
use Symfony\Component\Workflow\Workflow;
use Symfony\Component\Workflow\WorkflowEvents;

/**
 * 工作流注册机
 * @package Zrone\HyperfWorkflow
 */
class WorkflowRegistry
{
    public $config;

    /** @var Registry $registry */
    public $registry;

    /**
     * WorkflowRegistry constructor.
     */
    public function __construct(ConfigInterface $config)
    {
        $this->config = $config->get('workflow');
    }

    public function create() : Registry
    {
        if (!($this->registry instanceof Registry)) {
            $this->registry = new Registry();

            foreach ($this->config as $workflow) {
                $definitionBuilder = new DefinitionBuilder();

                list($storage, $prepareTrans) = $this->buildTransition($workflow['transitions'], $workflow['attaches']);

                $definition = $definitionBuilder->addPlaces($workflow['places'])
                    ->addTransitions($prepareTrans)
                    ->setMetadataStore($this->buildMetadataStore($workflow['places_metadata'], $storage))
                    ->build();

                $marking = new MarkingStore((bool)$workflow['single_state'], $workflow['property']);
                $wfInstance = new Workflow($definition, $marking, $this->buildDispatcher($workflow['dispatcher']));
                $this->registry->addWorkflow($wfInstance, new InstanceOfSupportStrategy($workflow['model']));
            }
        }

        return $this->registry;
    }

    /**
     * 触发器
     *
     * @param array|null $dispatcher
     */
    private function buildDispatcher(?array $dispatcher): EventDispatcher
    {
        $dispatcher = new EventDispatcher();

        foreach ($dispatcher as $eventName => $closure) {
            if (!in_array($eventName, WorkflowEvents::ALIASES) || method_exists($closure[0], $closure[1])) continue;
            $dispatcher->addListener($eventName, function () use ($closure) {
                call_user_func($closure);
            });
        }
        return $dispatcher;
    }

    /**
     * 注册工作流
     *
     * @param array|null $transitions
     * @param array $attach
     * @return array
     */
    private function buildTransition(?array $transitions, array $attach = []): array
    {
        $storage = new \SplObjectStorage();
        $prepareTrans = [];

        foreach ($transitions as $transition) {
            $transition = new Transition($transitions['name'], $transitions['from'], $transitions['to']);
            isset($attach[$transitions['name']]) && $storage->attach($transition, ['label' => $attach[$transitions['name']]]);

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
    private function buildMetadataStore(?array $metadata, \SplObjectStorage $storage)
    {
        return new InMemoryMetadataStore([], $metadata, $storage);
    }
}