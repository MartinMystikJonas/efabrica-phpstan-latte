<?php

declare(strict_types=1);

namespace Efabrica\PHPStanLatte\Compiler\NodeVisitor;

use Efabrica\PHPStanLatte\Compiler\NodeVisitor\Behavior\ActualClassNodeVisitorBehavior;
use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Expression;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;

final class RemoveExtractParamsNodeVisitor extends NodeVisitorAbstract implements PostCompileNodeVisitorInterface
{
    use ActualClassNodeVisitorBehavior;

    public function leaveNode(Node $node): ?int
    {
        if (!$node instanceof Expression) {
            return null;
        }

        $expr = $node->expr;
        if (!$expr instanceof FuncCall) {
            return null;
        }

        if (!$expr->name instanceof Name) {
            return null;
        }

        if ((string)$expr->name === 'extract') {
            return NodeTraverser::REMOVE_NODE;
        }

        // todo arg this->params

        return null;
    }
}
