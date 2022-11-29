<?php

declare(strict_types=1);

namespace Efabrica\PHPStanLatte\Tests\Rule\LatteTemplatesRule;

use Efabrica\PHPStanLatte\Collector\Finder\MethodCallFinder;
use Efabrica\PHPStanLatte\Collector\Finder\ResolvedNodeFinder;
use Efabrica\PHPStanLatte\Collector\Finder\VariableFinder;
use Efabrica\PHPStanLatte\LatteTemplateResolver\LatteTemplateResolverInterface;
use Efabrica\PHPStanLatte\Template\Component;
use Efabrica\PHPStanLatte\Template\Variable;
use Nette\Utils\Strings;
use PhpParser\Node;
use PHPStan\Analyser\Error;
use PHPStan\Analyser\Scope;

use PHPStan\Node\CollectedDataNode;
use PHPStan\PhpDoc\TypeStringResolver;
use PHPStan\Rules\Rule;

/**
 * @implements Rule<CollectedDataNode>
 */
final class CollectorResultRule implements Rule
{
    /** @var LatteTemplateResolverInterface[] */
    private array $latteTemplateResolvers;

    /**
     * @param LatteTemplateResolverInterface[] $latteTemplateResolvers
     */
    public function __construct(array $latteTemplateResolvers) {
        $this->latteTemplateResolvers = $latteTemplateResolvers;

    }

    public function getNodeType(): string
    {
        return CollectedDataNode::class;
    }

    /**
     * @param CollectedDataNode $collectedDataNode
     */
    public function processNode(Node $collectedDataNode, Scope $scope): array
    {
        $errors = [];

        $resolvedNodeFinder = new ResolvedNodeFinder($collectedDataNode);
        foreach ($this->latteTemplateResolvers as $latteTemplateResolver) {
            foreach ($resolvedNodeFinder->find(get_class($latteTemplateResolver)) as $collectedResolvedNode) {
                $resolver = Strings::after($collectedResolvedNode->getResolver(), '\\', -1);
                $errors[] = new Error(
                    "NODE $resolver " .
                    str_replace(__NAMESPACE__, '@', str_replace('\\\\', '\\', json_encode($collectedResolvedNode->getParams(), JSON_UNESCAPED_SLASHES))),
                    $scope->getFile()
                );
                $templates = $latteTemplateResolver->findTemplates($collectedResolvedNode, $collectedDataNode);
                foreach ($templates as $template) {
                    $path = pathinfo($template->getPath(), PATHINFO_BASENAME);
                    $presenter = Strings::after($template->getActualClass(), '\\', -1);
                    $variables = array_map(function(Variable $v) { return $v->getName(); }, $template->getVariables());
                    sort($variables);
                    $components = array_map(function(Component $v) { return $v->getName(); }, $template->getComponents());
                    sort($components);
                    $errors[] = new Error(
                    "TEMPLATE $path $presenter" .
                        " Variables: " . implode(', ', $variables) .
                        " Components: " . implode(', ', $components),
                        $template->getPath()
                    );
                }
            }
        }

        return $errors;
    }

    public function getPrinter(): callable {
        return static function (int $line, string $message, string $file, ?string $tip): string {
            $message = $file . ":" . sprintf('%02d: %s', $line, $message);
            return $message;
        };
    }
}
