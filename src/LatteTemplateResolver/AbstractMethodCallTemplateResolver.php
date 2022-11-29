<?php

declare(strict_types=1);

namespace Efabrica\PHPStanLatte\LatteTemplateResolver;

use Efabrica\PHPStanLatte\Collector\ValueObject\CollectedResolvedNode;
use Efabrica\PHPStanLatte\Template\Template;
use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Stmt\Class_;
use PHPStan\Analyser\Scope;
use PHPStan\BetterReflection\BetterReflection;
use PHPStan\BetterReflection\Reflection\ReflectionMethod;
use PHPStan\Node\CollectedDataNode;
use PHPStan\Type\ObjectType;

abstract class AbstractMethodCallTemplateResolver extends AbstractTemplateResolver
{
    protected const PARAM_CONTEXT_CLASS = 'contextClass';
    protected const PARAM_CONTEXT_METHOD = 'contextMethod';
    protected const PARAM_CLASS = 'class';
    protected const PARAM_METHOD = 'method';

    public function collect(Node $node, Scope $scope): ?CollectedResolvedNode
    {
        $classReflection = $scope->getClassReflection();
        if ($classReflection === null) {
            return null;
        }

        if (!$node instanceof MethodCall) {
            //file_put_contents("/app/test.log", get_class($node)."\n", FILE_APPEND);
            return null;
        }

        $calledOnClass = $scope->getType($node->var);
        if (!$calledOnClass instanceof ObjectType) {
            file_put_contents("/app/test.log", get_class($calledOnClass) . " onclass\n", FILE_APPEND);
            return null;
        }

        $calledOnClassName = (string)$calledOnClass->getClassName();
        if (!$calledOnClassName) {
        file_put_contents("/app/test.log", "classname\n", FILE_APPEND);
            return null;
        }

        $methodName = $node->name->name;

        file_put_contents("/app/test.log", "CHECK $calledOnClassName $methodName\n", FILE_APPEND);

        foreach ($this->getSupportedMethods() as $supportedMethod) {
            file_put_contents("/app/test.log", "CHECK $calledOnClassName < {$supportedMethod[0]} $methodName = {$supportedMethod[1]}\n", FILE_APPEND);
            if ($calledOnClass->isInstanceOf($supportedMethod[0])->yes() && $methodName == $supportedMethod[1]) {
            file_put_contents("/app/test.log", "OK $calledOnClassName < {$supportedMethod[0]} $methodName = {$supportedMethod[1]}\n", FILE_APPEND);
                return new CollectedResolvedNode(static::class, $this->createCollectedResolvedNodeParams($node, $scope, $calledOnClassName, $methodName));
            }
        }

        return null;
    }

    protected function createCollectedResolvedNodeParams(MethodCall $node, Scope $scope, string $calledOnClassName, string $methodName): array
    {
        return [
            self::PARAM_CLASS => $calledOnClassName,
            self::PARAM_METHOD => $methodName,
            self::PARAM_CONTEXT_CLASS => $scope->getClassReflection()->getName(),
            self::PARAM_CONTEXT_METHOD => $scope->getFunctionName(),
        ];
    }

    /**
     * @return Template[]
     */
    protected function getTemplates(CollectedResolvedNode $resolvedNode, CollectedDataNode $collectedDataNode): array
    {
        $contextClassName = $resolvedNode->getParam(self::PARAM_CONTEXT_CLASS);
        $contextMethodName = $resolvedNode->getParam(self::PARAM_CONTEXT_METHOD);
        $contextMethod = (new BetterReflection())->reflector()->reflectClass($contextClassName)->getMethod($contextMethodName);

        return $this->getMethodCallTemplates($resolvedNode, $collectedDataNode, $contextMethod);
    }

    /**
     * REturns array [class, method]
     * @return array<array{class-string, string}>
     */
    abstract protected function getSupportedMethods(): array;

    /**
     * @return Template[]
     */
    abstract protected function getMethodCallTemplates(CollectedResolvedNode $resolvedNode, CollectedDataNode $collectedDataNode, ReflectionMethod $contextMethod): array;
}
