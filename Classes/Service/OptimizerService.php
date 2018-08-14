<?php
namespace Flownative\ImageOptimizer\Service;

/**
 * This file is part of the Flownative.ImageOptimizer package.
 *
 * (c) 2018 Christian Müller, Flownative GmbH
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */

use Neos\Flow\Annotations as Flow;
use Neos\Flow\ResourceManagement\PersistentResource;
use Neos\Flow\ResourceManagement\ResourceManager;
use Neos\Flow\Utility\Algorithms;
use Neos\Flow\Utility\Environment;
use Neos\Utility\Files;

/**
 * Service to optimize file streams and return optimized Resources
 *
 * @Flow\Scope("singleton")
 */
class OptimizerService
{
    /**
     * @Flow\Inject
     * @var ResourceManager
     */
    protected $resourceManager;

    /**
     * @Flow\Inject
     * @var Environment
     */
    protected $environment;

    /**
     * Tries to optimize the binary content provided via $stream, using the given $filename and $optimizationConfiguration.
     * Returning a PersistentResource stored in the given $resourceCollectionName
     *
     * @param resource $stream file/binary stream resource handler (fopen)
     * @param string $filename
     * @param string $resourceCollectionName
     * @param OptimizerConfiguration $optimizationConfiguration
     * @return PersistentResource
     * @throws \Neos\Flow\ResourceManagement\Exception
     * @throws \Neos\Eel\Exception
     * @throws \RuntimeException
     */
    public function optimize($stream, string $filename, string $resourceCollectionName, OptimizerConfiguration $optimizationConfiguration): PersistentResource
    {
        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        $outFileExtension = $optimizationConfiguration->getOutFileExtension() !== '' ? $optimizationConfiguration->getOutFileExtension() : $extension;
        $originalTemporaryPathAndFilename = $this->generateTemporaryPathAndFilename('OptimizerOriginal-' . Algorithms::generateRandomString(13), $extension);
        $optimizedTemporaryPathAndFilename = $this->generateTemporaryPathAndFilename(pathinfo($filename, PATHINFO_FILENAME) . 'Optim', $outFileExtension);

        $originalTemporaryStream = fopen($originalTemporaryPathAndFilename, 'w+');
        stream_copy_to_stream($stream, $originalTemporaryStream);
        fclose($originalTemporaryStream);

        $commandString = $optimizationConfiguration->getPreparedCommandString(['originalPath' => escapeshellarg($originalTemporaryPathAndFilename), 'optimizedPath' => escapeshellarg($optimizedTemporaryPathAndFilename)]);
        exec($commandString, $output, $result);

        if (!file_exists($optimizedTemporaryPathAndFilename)) {
            Files::unlink($originalTemporaryPathAndFilename);
            throw new \RuntimeException('Optimization not successful with exit status ' . $result . ' and the following output: ' . implode(chr(10), $output));
        }

        $bestResultPathAndFilename = (filesize($originalTemporaryPathAndFilename) <= filesize($optimizedTemporaryPathAndFilename)) ? $originalTemporaryPathAndFilename : $optimizedTemporaryPathAndFilename;
        $resource = $this->resourceManager->importResource($bestResultPathAndFilename, $resourceCollectionName);
        $resource->setFilename($filename);
        Files::unlink($originalTemporaryPathAndFilename);
        Files::unlink($optimizedTemporaryPathAndFilename);

        return $resource;
    }

    /**
     * @param string $filename
     * @param string $extension
     * @return string
     */
    protected function generateTemporaryPathAndFilename(string $filename, string $extension): string
    {
        return ($this->environment->getPathToTemporaryDirectory() . $filename . '.' . $extension);
    }
}
