<?php

declare(strict_types=1);

namespace Efabrica\PHPStanLatte\VariableCollector;

use Efabrica\PHPStanLatte\Template\Variable;
use PHPStan\Type\ArrayType;
use PHPStan\Type\MixedType;
use PHPStan\Type\ObjectType;
use PHPStan\Type\StringType;

final class DefaultTemplateVariables implements VariableCollectorInterface
{
    /**
     * @return Variable[]
     */
    public function collect(): array
    {
        $variables = [];
        $variables[] = new Variable('baseUrl', new StringType());
        $variables[] = new Variable('basePath', new StringType());
        $variables[] = new Variable('ʟ_fi', new ObjectType('Latte\Runtime\FilterInfo'));

        // nette\security bridge
        $variables[] = new Variable('user', new ObjectType('Nette\Security\User'));

        // nette\application bridge
        $variables[] = new Variable('presenter', new ObjectType('Nette\Application\UI\Presenter'));
        $variables[] = new Variable('control', new ObjectType('Nette\Application\UI\Control'));

        $flashesArrayType = new ArrayType(new MixedType(), new ObjectType('stdClass'));
        $variables[] = new Variable('flashes', $flashesArrayType);

        return $variables;
    }
}
