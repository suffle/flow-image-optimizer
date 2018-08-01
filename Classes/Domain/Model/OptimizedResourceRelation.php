<?php
namespace Flownative\ImageOptimizer\Domain\Model;

use Neos\Flow\Annotations as Flow;
use Doctrine\ORM\Mapping as ORM;
use Neos\Flow\ResourceManagement\PersistentResource;

/**
 * @Flow\Entity
 */
class OptimizedResourceRelation
{
    /**
     * @Flow\Identity
     * @ORM\Id
     * @var string
     */
    protected $originalResourceIdentificationHash;

    /**
     * @var PersistentResource
     * @ORM\OneToOne(cascade={"PERSIST", "REMOVE"}, orphanRemoval=true)
     */
    protected $optimizedResource;

    /**
     * OptimizedResourceRelation constructor.
     *
     * @param string $originalResourceIdentificationHash
     * @param PersistentResource $optimizedResource
     */
    public function __construct(string $originalResourceIdentificationHash, PersistentResource $optimizedResource)
    {
        $this->originalResourceIdentificationHash = $originalResourceIdentificationHash;
        $this->optimizedResource = $optimizedResource;
    }

    /**
     * @param string $sha1
     * @param string $filename
     * @param PersistentResource $optimizedResource
     * @return OptimizedResourceRelation
     */
    public static function createFromResourceSha1AndFilename(string $sha1, string $filename, PersistentResource $optimizedResource): OptimizedResourceRelation
    {
        return new static(static::createOriginalResourceIdentificationHash($sha1, $filename), $optimizedResource);
    }

    /**
     * @param string $sha1
     * @param string $filename
     * @return string
     */
    public static function createOriginalResourceIdentificationHash($sha1, $filename)
    {
        return hash('sha256', $sha1 . $filename);
    }

    /**
     * @return string
     */
    public function getOriginalResourceIdentificationHash(): string
    {
        return $this->originalResourceIdentificationHash;
    }

    /**
     * @return PersistentResource
     */
    public function getOptimizedResource(): PersistentResource
    {
        return $this->optimizedResource;
    }
}
