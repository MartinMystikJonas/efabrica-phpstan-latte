<?php

declare(strict_types=1);

namespace Efabrica\PHPStanLatte\LatteTemplateResolver;

use Efabrica\PHPStanLatte\Collector\ValueObject\CollectedResolvedNode;
use Efabrica\PHPStanLatte\Template\Template;
use Efabrica\PHPStanLatte\Template\Variable;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\BetterReflection\Reflection\ReflectionMethod;
use PHPStan\Node\CollectedDataNode;
use PHPStan\Type\ObjectType;

final class NetteApplicationUIControlRender extends AbstractMethodCallTemplateResolver
{
    private const PARAM_TEMPLATE_FILE = 'templateFile';
    private const PARAM_TEMPLATE_PARAMS = 'templateParams';

    public function getSupportedMethods(): array
    {
        return [
            ['Nette\Application\UI\Template', 'render'],
            ['Nette\Application\UI\ITemplate', 'render'],
            ['Latte\Runtime\Template', 'render'],
            ['Latte\Runtime\Template', 'renderToString'],
        ];
    }

    protected function createCollectedResolvedNodeParams(MethodCall $node, Scope $scope, string $calledOnClassName, string $methodName): array
    {
        $explicitTemplateFile = null;
        $explicitParams = null;

        $arg = $node->getArgs()[0] ?? null;
        if ($arg) {
            /** @var string|null $explicitTemplateFile */
            $explicitTemplateFile = $this->valueResolver->resolve($arg->value, $scope->getFile());
        }

        $arg = $node->getArgs()[1] ?? null;
        if ($arg && $scope->getType($arg->value)->isArray()) {
            $explicitParams = ['array' => 'string'];
        } elseif ($arg && $scope->getType($arg->value) instanceof ObjectType) {
            $explicitParams = ['object' => 'string'];
        }

        return array_merge(parent::createCollectedResolvedNodeParams($node, $scope, $calledOnClassName, $methodName), [
            self::PARAM_TEMPLATE_FILE => $explicitTemplateFile,
            self::PARAM_TEMPLATE_PARAMS => $explicitParams,
        ]);
    }

    protected function getMethodCallTemplates(CollectedResolvedNode $resolvedNode, CollectedDataNode $collectedDataNode, ReflectionMethod $contextMethod): array
    {
        $contextClassName = $resolvedNode->getParam(self::PARAM_CONTEXT_CLASS);
        $controlType = new ObjectType($contextClassName);
        $globalVariables = [
            new Variable('actualClass', $controlType),
            new Variable('presenter', $controlType),
        ];

        $globalComponents = $this->componentFinder->find($contextClassName, '');

        $explicitParams = $resolvedNode->getParam(self::PARAM_TEMPLATE_PARAMS) ?? [];

        $variables = array_merge($globalVariables, $this->variableFinder->findFromCallersByMethod($contextMethod), $explicitParams);
        $components = array_merge($globalComponents, $this->componentFinder->findFromCallersByMethod($contextMethod));

        $explicitTemplateFile = $resolvedNode->getParam(self::PARAM_TEMPLATE_FILE);
        if ($explicitTemplateFile !== null) {
            $templatePaths = [$explicitTemplateFile];
        } else {
            $templatePaths = $this->templatePathFinder->findByMethod($contextMethod);
        }
        foreach ($templatePaths as $templatePath) {
            $templates[] = new Template($templatePath, $contextClassName, $variables, $components);
        }
        return $templates;
    }
}
