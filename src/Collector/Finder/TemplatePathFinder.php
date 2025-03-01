<?php

declare(strict_types=1);

namespace Efabrica\PHPStanLatte\Collector\Finder;

use Efabrica\PHPStanLatte\Collector\TemplatePathCollector;
use Efabrica\PHPStanLatte\Collector\ValueObject\CollectedTemplatePath;
use PHPStan\BetterReflection\Reflection\ReflectionMethod;
use PHPStan\Node\CollectedDataNode;

/**
 * @phpstan-import-type CollectedTemplatePathArray from CollectedTemplatePath
 */
final class TemplatePathFinder
{
    /**
     * @var array<string, array<string, string[]>>
     */
    private array $collectedTemplatePaths;

    public function __construct(CollectedDataNode $collectedDataNode)
    {
        $collectedTemplatePaths = $this->buildData(array_filter(array_merge(...array_values($collectedDataNode->get(TemplatePathCollector::class)))));
        foreach ($collectedTemplatePaths as $collectedTemplatePath) {
            $className = $collectedTemplatePath->getClassName();
            $methodName = $collectedTemplatePath->getMethodName();
            if (!isset($this->collectedTemplatePaths[$className][$methodName])) {
                $this->collectedTemplatePaths[$className][$methodName] = [];
            }
            $this->collectedTemplatePaths[$className][$methodName][] = $collectedTemplatePath->getTemplatePath();
        }
    }

    /**
     * @return string[]
     */
    public function find(string $className, string $methodName): array
    {
        return $this->collectedTemplatePaths[$className][$methodName] ?? [];
    }

    /**
     * @return string[]
     */
    public function findByMethod(ReflectionMethod $method): array
    {
        return $this->find($method->getDeclaringClass()->getName(), $method->getName());
    }

    /**
     * @phpstan-param array<CollectedTemplatePathArray> $data
     * @return CollectedTemplatePath[]
     */
    private function buildData(array $data): array
    {
        $collectedTemplatePaths = [];
        foreach ($data as $item) {
            $collectedTemplatePaths[] = CollectedTemplatePath::fromArray($item);
        }
        return $collectedTemplatePaths;
    }
}
