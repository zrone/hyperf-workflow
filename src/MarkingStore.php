<?php

declare(strict_types=1);
/**
 * application for lyky.
 *
 * @author   zrone<xujining2008@126.com>
 */
namespace Zrone\HyperfWorkflow;

use Zrone\Component\Workflow\Marking;
use Zrone\Component\Workflow\MarkingStore\MarkingStoreInterface;

/**
 * MethodMarkingStore stores the marking with a subject's method.
 *
 * This store deals with a "single state" or "multiple state" Marking.
 *
 * "single state" Marking means a subject can be in one and only one state at
 * the same time. Use it with state machine.
 *
 * "multiple state" Marking means a subject can be in many states at the same
 * time. Use it with workflow.
 *
 * @package Zrone\HyperfWorkflow
 */
final class MarkingStore implements MarkingStoreInterface
{
    private $singleState;

    private $property;

    /**
     * @param string $property Used to determine methods to call
     *                         The `getMarking` method will use `$subject->getProperty()`
     *                         The `setMarking` method will use `$subject->setProperty(string|array $places, array $context = array())`
     */
    public function __construct(bool $singleState = false, string $property = 'marking')
    {
        $this->singleState = $singleState;
        $this->property = $property;
    }

    /**
     * {@inheritdoc}
     */
    public function getMarking(object $subject): Marking
    {
        $marking = $subject->{$this->property};

        if ($marking === null) {
            return new Marking();
        }

        if ($this->singleState) {
            $marking = [(string) $marking => 1];
        }

        return new Marking($marking);
    }

    /**
     * {@inheritdoc}
     */
    public function setMarking(object $subject, Marking $marking, array $context = [])
    {
        $marking = $marking->getPlaces();

        if ($this->singleState) {
            $marking = key($marking);
        }

        $subject->{$this->property} = $marking;
    }
}
