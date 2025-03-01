<?php

declare(strict_types=1);

namespace Efabrica\PHPStanLatte\Tests\Rule\LatteTemplatesRule\PresenterWithoutModule\Source;

use Nette\Application\UI\Control;

final class SomeControl extends Control
{
    public function __construct()
    {
        $this['header'] = new SomeHeaderControl();
    }

    public function render(): void
    {
    }

    public function renderOtherRender(): void
    {
    }

    protected function createComponentBody(): SomeBodyControl
    {
        return new SomeBodyControl();
    }

    protected function createComponentFooter(): SomeFooterControl
    {
        return new SomeFooterControl();
    }
}
