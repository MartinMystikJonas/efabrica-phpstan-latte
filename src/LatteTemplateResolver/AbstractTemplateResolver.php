<?php

declare(strict_types=1);

namespace Efabrica\PHPStanLatte\LatteTemplateResolver;

use Efabrica\PHPStanLatte\Collector\Finder\ComponentFinder;
use Efabrica\PHPStanLatte\Collector\Finder\MethodCallFinder;
use Efabrica\PHPStanLatte\Collector\Finder\TemplatePathFinder;
use Efabrica\PHPStanLatte\Collector\Finder\VariableFinder;
use Efabrica\PHPStanLatte\Collector\ValueObject\CollectedResolvedNode;
use Efabrica\PHPStanLatte\Template\Template;
use PHPStan\BetterReflection\Reflection\ReflectionClass;
use PHPStan\BetterReflection\Reflection\ReflectionMethod;
use PHPStan\Node\CollectedDataNode;
use PHPStan\PhpDoc\TypeStringResolver;

abstract class AbstractTemplateResolver implements LatteTemplateResolverInterface
{
    private TypeStringResolver $typeStringResolver;

    protected MethodCallFinder $methodCallFinder;

    protected VariableFinder $variableFinder;

    protected ComponentFinder $componentFinder;

    protected TemplatePathFinder $templatePathFinder;

    public function __construct(TypeStringResolver $typeStringResolver)
    {
        $this->typeStringResolver = $typeStringResolver;
    }

    public function findTemplates(CollectedResolvedNode $resolvedNode, CollectedDataNode $collectedDataNode): array
    {
        // TODO create factories?
        $this->methodCallFinder = new MethodCallFinder($collectedDataNode);
        $this->variableFinder = new VariableFinder($collectedDataNode, $this->methodCallFinder, $this->typeStringResolver);
        $this->componentFinder = new ComponentFinder($collectedDataNode, $this->methodCallFinder, $this->typeStringResolver);
        $this->templatePathFinder = new TemplatePathFinder($collectedDataNode);

        return $this->getTemplates($resolvedNode, $collectedDataNode);
    }

    /**
     * @return ReflectionMethod[]
     */
    protected function getMethodsMatching(ReflectionClass $reflectionClass, string $pattern): array
    {
        $methods = [];
        foreach ($reflectionClass->getMethods() as $reflectionMethod) {
            if (preg_match($pattern, $reflectionMethod->getName()) !== false) {
                $methods[] = $reflectionMethod;
            }
        }
        return $methods;
    }

    /**
     * @return Template[]
     */
    abstract protected function getTemplates(CollectedResolvedNode $resolvedNode, CollectedDataNode $collectedDataNode): array;
}
