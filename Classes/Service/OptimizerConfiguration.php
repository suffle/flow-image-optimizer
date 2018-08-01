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

use Neos\Eel\CompilingEvaluator;
use Neos\Eel\Utility;
use Neos\Flow\Annotations as Flow;

/**
 * A plain object to hold configuration for a specific optimizer.
 */
class OptimizerConfiguration
{
    /**
     * @var string
     */
    protected $binaryPath = '';

    /**
     * @var string
     */
    protected $argumentsExpression = '';

    /**
     * @var string
     */
    protected $outFileExtension;

    /**
     * @Flow\Inject(lazy=false)
     * @var CompilingEvaluator
     */
    protected $eelEvaluator;

    /**
     * OptimizerConfiguration constructor.
     *
     * @param string $binaryPath Path to the binary that should do the optimization of the resource.
     * @param string $argumentsExpression An EEL expression to build the argumetns for the transformation
     * @param string $outFileExtension Used to overwrite the outfile extension if needed. (Eg. if the optimizer transforms from JPEG to PNG).
     */
    public function __construct(string $binaryPath, string $argumentsExpression, string $outFileExtension)
    {
        $this->binaryPath = $binaryPath;
        $this->argumentsExpression = $argumentsExpression;
        $this->outFileExtension = $outFileExtension;
    }

    /**
     * @return string
     */
    public function getBinaryPath(): string
    {
        return $this->binaryPath;
    }

    /**
     * @return string
     */
    public function getArgumentsExpression(): string
    {
        return $this->argumentsExpression;
    }

    /**
     * @return string
     */
    public function getOutFileExtension(): string
    {
        return $this->outFileExtension;
    }

    /**
     * @param array $contextVariables
     * @return mixed
     * @throws \Neos\Eel\Exception
     */
    protected function getArguments(array $contextVariables)
    {
        return Utility::evaluateEelExpression($this->argumentsExpression, $this->eelEvaluator, $contextVariables);
    }

    /**
     * The result should be directly callable via "exec" for example.
     *
     * @param array $contextVariables
     * @return string
     * @throws \Neos\Eel\Exception
     */
    public function getPreparedCommandString(array $contextVariables)
    {
        return $this->getBinaryPath() . ' ' . $this->getArguments($contextVariables);
    }
}
