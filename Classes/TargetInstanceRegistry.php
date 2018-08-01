<?php
namespace Flownative\ImageOptimizer;

use Neos\Flow\Annotations as Flow;

/**
 * @Flow\Scope("singleton")
 */
class TargetInstanceRegistry
{
    /**
     * @var ImageOptimizerTarget[]
     */
    protected $targetInstances = [];

    /**
     * @param ImageOptimizerTarget $target
     */
    public function register(ImageOptimizerTarget $target)
    {
        $this->targetInstances[] = $target;
    }

    /**
     * @return ImageOptimizerTarget[]
     */
    public function getRegisteredInstances(): array
    {
        return $this->targetInstances;
    }
}
