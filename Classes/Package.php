<?php
namespace Flownative\ImageOptimizer;

use Neos\Flow\Core\Bootstrap;
use Neos\Flow\Persistence\PersistenceManagerInterface;
use Neos\Flow\SignalSlot\Dispatcher;

/**
 * Package object for Flow package management
 */
class Package extends \Neos\Flow\Package\Package
{

    /**
     * Persist changes to our optimizer target.
     *
     * @param Core\Bootstrap $bootstrap The current bootstrap
     * @return void
     */
    public function boot(Bootstrap $bootstrap)
    {
        $dispatcher = $bootstrap->getEarlyInstance(Dispatcher::class);
        $dispatcher->connect(Bootstrap::class, 'finishedRuntimeRun', function () use ($bootstrap) {
            /** @var TargetInstanceRegistry $registry */
            $registry = $bootstrap->getObjectManager()->get(TargetInstanceRegistry::class);
            foreach ($registry->getRegisteredInstances() as $targetInstance) {
                $targetInstance->persist();
            }
        });
    }
}
