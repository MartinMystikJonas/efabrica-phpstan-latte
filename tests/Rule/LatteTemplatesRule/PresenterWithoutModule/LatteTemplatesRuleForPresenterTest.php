<?php

declare(strict_types=1);

namespace Efabrica\PHPStanLatte\Tests\Rule\LatteTemplatesRule\PresenterWithoutModule;

use Efabrica\PHPStanLatte\Tests\Rule\LatteTemplatesRule\LatteTemplatesRuleTest;
use Efabrica\PHPStanLatte\Tests\Rule\LatteTemplatesRule\PresenterWithoutModule\Fixtures\LinksPresenter;
use Efabrica\PHPStanLatte\Tests\Rule\LatteTemplatesRule\PresenterWithoutModule\Source\SomeControl;

final class LatteTemplatesRuleForPresenterTest extends LatteTemplatesRuleTest
{
    public static function getAdditionalConfigFiles(): array
    {
        return [
            __DIR__ . '/../../../../rules.neon',
            __DIR__ . '/../../../config.neon',
            __DIR__ . '/config.neon',
        ];
    }

    public function testVariables(): void
    {
        $this->analyse([__DIR__ . '/Fixtures/VariablesPresenter.php', __DIR__ . '/Fixtures/ParentPresenter.php'], [
            [
                'Variable $items might not be defined.',
                5,
                'default.latte',
            ],
            [
                'Variable $nonExistingVariable might not be defined.',
                18,
                'default.latte',
            ],
            [
                'Variable $fromOtherAction might not be defined.',
                19,
                'default.latte',
            ],
            [
                'Variable $fromRenderDefault might not be defined.',
                4,
                'other.latte',
            ],
            [
                'Variable $nonExistingVariable might not be defined.',
                5,
                'other.latte',
            ],
            [
                'Variable $nonExistingVariable might not be defined.',
                5,
                'empty.latte',
            ],
            [
                'Variable $nonExistingVariable might not be defined.',
                4,
                'parent.latte',
            ],
            [
                'Variable $nonExistingVariable might not be defined.',
                5,
                'noAction.latte',
            ],
        ]);
    }

    public function testComponents(): void
    {
        $this->analyse([
            __DIR__ . '/Fixtures/ComponentsPresenter.php',
            __DIR__ . '/Fixtures/ParentPresenter.php',
            __DIR__ . '/Source/ControlRegistrator.php',
            __DIR__ . '/Source/SomeBodyControl.php',
            __DIR__ . '/Source/SomeControl.php',
            __DIR__ . '/Source/SomeFooterControl.php',
            __DIR__ . '/Source/SomeHeaderControl.php',
            __DIR__ . '/Source/SomeTableControl.php',
        ], [
            [
                'Component with name "onlyCreateForm" probably doesn\'t exist.',
                9,
                'default.latte',
            ],
            [
                'Component with name "nonExistingControl" probably doesn\'t exist.',
                11,
                'default.latte',
            ],
            [
                'Call to an undefined method ' . SomeControl::class . '::renderNonExistingRender().',
                17,
                'default.latte',
            ],
            [
                'Component with name "someControl-nonexisting" probably doesn\'t exist.',
                23,
                'default.latte',
            ],
            [
                'Component with name "onlyParentDefaultForm" probably doesn\'t exist.',
                7,
                'create.latte',
            ],
            [
                'Component with name "nonExistingControl" probably doesn\'t exist.',
                11,
                'create.latte',
            ],
            [
                'Component with name "someControl" probably doesn\'t exist.',
                13,
                'create.latte',
            ],
            [
                'Component with name "someControl-header" probably doesn\'t exist.',
                14,
                'create.latte',
            ],
            [
                'Component with name "someControl-body" probably doesn\'t exist.',
                15,
                'create.latte',
            ],
            [
                'Component with name "someControl-body-table" probably doesn\'t exist.',
                16,
                'create.latte',
            ],
            [
                'Component with name "someControl-footer" probably doesn\'t exist.',
                17,
                'create.latte',
            ],
            [
                'Component with name "someControl-nonexisting" probably doesn\'t exist.',
                18,
                'create.latte',
            ],
            [
                'Component with name "nonExistingControl" probably doesn\'t exist.',
                3,
                'parent.latte',
            ],
            [
                'Component with name "onlyParentDefaultForm" probably doesn\'t exist.',
                7,
                'noAction.latte',
            ],
            [
                'Component with name "onlyCreateForm" probably doesn\'t exist.',
                9,
                'noAction.latte',
            ],
            [
                'Component with name "nonExistingControl" probably doesn\'t exist.',
                11,
                'noAction.latte',
            ],
        ]);
    }

    public function testFilters(): void
    {
        $this->analyse([__DIR__ . '/Fixtures/FiltersPresenter.php', __DIR__ . '/Fixtures/ParentPresenter.php'], [
            [
                'Undefined latte filter "nonExistingFilter".',
                2,
                'default.latte',
                'Register it in phpstan.neon: parameters > latte > filters. See https://github.com/efabrica-team/phpstan-latte#filters',
            ],
        ]);
    }

    public function testLinks(): void
    {
        $this->analyse([__DIR__ . '/Fixtures/LinksPresenter.php', __DIR__ . '/Fixtures/ParentPresenter.php'], [
            [
                'Method ' . LinksPresenter::class . '::actionCreate() invoked with 1 parameter, 0 required.',
                7,
                'default.latte',
            ],
            [
                'Method ' . LinksPresenter::class . '::actionCreate() invoked with 1 parameter, 0 required.',
                8,
                'default.latte',
            ],
            [
                'Method ' . LinksPresenter::class . '::actionCreate() invoked with 1 parameter, 0 required.',
                10,
                'default.latte',
            ],
            [
                'Method ' . LinksPresenter::class . '::actionCreate() invoked with 1 parameter, 0 required.',
                11,
                'default.latte',
            ],
            [
                'Method ' . LinksPresenter::class . '::actionCreate() invoked with 1 parameter, 0 required.',
                13,
                'default.latte',
            ],
            [
                'Method ' . LinksPresenter::class . '::actionCreate() invoked with 1 parameter, 0 required.',
                14,
                'default.latte',
            ],
            [
                'Method ' . LinksPresenter::class . '::actionCreate() invoked with 1 parameter, 0 required.',
                16,
                'default.latte',
            ],
            [
                'Method ' . LinksPresenter::class . '::actionCreate() invoked with 1 parameter, 0 required.',
                17,
                'default.latte',
            ],
            [
                'Method ' . LinksPresenter::class . '::actionEdit() invoked with 0 parameters, 1-2 required.',
                20,
                'default.latte',
            ],
            [
                'Method ' . LinksPresenter::class . '::actionEdit() invoked with 0 parameters, 1-2 required.',
                21,
                'default.latte',
            ],
            [
                'Parameter #1 $id of method ' . LinksPresenter::class . '::actionEdit() expects string, array<string, string> given.',
                26,
                'default.latte',
            ],
            [
                'Parameter #1 $id of method ' . LinksPresenter::class . '::actionEdit() expects string, array<string, string> given.',
                27,
                'default.latte',
            ],
            [
                'Parameter #1 $id of method ' . LinksPresenter::class . '::actionEdit() expects string, array<string, int|string> given.',
                38,
                'default.latte',
            ],
            [
                'Parameter #1 $id of method ' . LinksPresenter::class . '::actionEdit() expects string, array<string, int|string> given.',
                39,
                'default.latte',
            ],
            [
                'Method ' . LinksPresenter::class . '::actionEdit() invoked with 3 parameters, 1-2 required.',
                56,
                'default.latte',
            ],
            [
                'Method ' . LinksPresenter::class . '::actionEdit() invoked with 3 parameters, 1-2 required.',
                57,
                'default.latte',
            ],
            [
                'Method ' . LinksPresenter::class . '::actionEdit() invoked with 3 parameters, 1-2 required.',
                59,
                'default.latte',
            ],
            [
                'Method ' . LinksPresenter::class . '::actionEdit() invoked with 3 parameters, 1-2 required.',
                60,
                'default.latte',
            ],
            [
                'Cannot load presenter \'Links:Invalid\', class \'Efabrica\PHPStanLatte\Tests\Rule\LatteTemplatesRule\PresenterWithoutModule\Fixtures\LinksModule\InvalidPresenter\' was not found.',
                63,
                'default.latte',
                'Check if your PHPStan configuration for latte > applicationMapping is correct. See https://github.com/efabrica-team/phpstan-latte#applicationmapping',
            ],
        ]);
    }
}
