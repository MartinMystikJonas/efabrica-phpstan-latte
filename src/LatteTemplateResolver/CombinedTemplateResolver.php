<?php

declare(strict_types=1);

namespace Efabrica\PHPStanLatte\LatteTemplateResolver;

use Efabrica\PHPStanLatte\Collector\ValueObject\CollectedResolvedNode;
use Efabrica\PHPStanLatte\Template\Template;
use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Node\CollectedDataNode;

final class CombinedTemplateResolver implements LatteTemplateResolverInterface
{
    private array $latteTemplateResolvers;

    public function __construct(array $latteTemplateResolvers)
    {
        foreach ($latteTemplateResolvers as $latteTemplateResolver) {
            $this->latteTemplateResolvers[get_class($latteTemplateResolver)] = $latteTemplateResolver;
        }
    }

    /** Try collect node in actual scope */
    public function collect(Node $node, Scope $scope): ?CollectedResolvedNode
    {
        foreach ($this->latteTemplateResolvers as $latteTemplateResolver) {
            $resolvedNode = $latteTemplateResolver->collect($node, $scope);
            if ($resolvedNode !== null) {
                $resolvedNodes[] = $resolvedNode->toArray();
            }
        }
        return count($resolvedNodes) > 0 ? $resolvedNodes : null;
    }

    /**
     * @return Template[]
     */
    public function findTemplates(CollectedResolvedNode $resolvedNode, CollectedDataNode $collectedDataNode): array
    {
    }
}
