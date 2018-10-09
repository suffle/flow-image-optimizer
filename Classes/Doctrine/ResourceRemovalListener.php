<?php
namespace Flownative\ImageOptimizer\Doctrine;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Flownative\ImageOptimizer\Domain\Model\OptimizedResourceRelation;
use Flownative\ImageOptimizer\Domain\Repository\OptimizedResourceRelationRepository;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\ResourceManagement\PersistentResource;

/**
 * @Flow\Scope("singleton")
 */
class ResourceRemovalListener
{
    /**
     * @Flow\Inject
     * @var OptimizedResourceRelationRepository
     */
    protected $optimizedResourceRelationRepository;

    /**
     * @param LifecycleEventArgs $event
     * @throws \Neos\Flow\Persistence\Exception\IllegalObjectTypeException
     */
    public function preRemove(LifecycleEventArgs $event)
    {
        if (!$event->getEntity() instanceof PersistentResource) {
            return;
        }

        $optimizedResourceRelation = $this->optimizedResourceRelationRepository->findOneByOptimizedResource($event->getEntity());
        if ($optimizedResourceRelation instanceof OptimizedResourceRelation) {
            $this->optimizedResourceRelationRepository->remove($optimizedResourceRelation);
        }
    }
}
