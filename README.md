# PHPStan latte
PHPStan extension to check compiled latte templates in context of Presenter or Component etc.
It is based on Tomas Votruba's [blog series](https://tomasvotruba.com/blog/stamp-static-analysis-of-templates/) and his packages symplify and reveal.

## Installation
```shell
composer require efabrica/phpstan-latte --dev
```

Add this line to your phpstan.neon:
```neon
includes:
    - vendor/efabrica/phpstan-latte/rules.neon
```

## Setup
You can also add some parameters for phpstan-latte extension. All of them are under `latte` key in `parameters` section.

### strictMode
Type: `bool`

Defines if compiled template is declared as strict (`declare(strict_types=1);`).

Default:
```neon
parameters:
    latte:
        strictMode: false
```

Example:
```neon
parameters:
    latte:
        strictMode: true
```

### engineBootstrap
Type: `string`

If provided this return value of this php file is used as Latte Engine.

If not provided default Latte Engine is used.

Example:
```neon
parameters:
    latte:
        engineBootstrap: latte.engine.php
```

Example `latte.engine.php`:

```php
<?php

$engine = new \Latte\Engine();
// ...
return $engine;
```

Example `latte.engine.php` for loading configured Engine from Nette application container:

```php
<?php

return App\Bootstrap::boot()->createContainer()->getService("latte.templateFactory")->createTemplate()->getLatte();
```

### macros (Latte 2 only)
Type: `array`

List of methods to register macros in format `Class::method`.

Example:
```neon
parameters:
    latte:
        macros:
            - MyMacro::install
```

### extensions (Latte 3 only)
Type: `array`

List of Latte extension classes

Example:
```neon
parameters:
    latte:
        macros:
            - MyMacro::install
```

### filters
Type: `array`

List of filters used in your apps. Name of filter is used as key, callback or function name is value.

For Latte 2 only default filters are added by default.

For Latte 3 default filters and filters from all extensions are added by default.

Default:
```neon
parameters:
    latte:
        filters:
            translate: [Nette\Localization\Translator, translate]
```

Example:
```neon
parameters:
    latte:
        filters:
            myFilter: [My\Global\Type, doFoo]
            functionFilter: strlen
```     

### globalVariables
Type: `array`

List of variables and their types which are always defined in all your templates.

Default:
```neon
parameters:
    latte:
        globalVariables: []
```

Example:
```neon
parameters:
    latte:
        globalVariables:
            myGlobalStringVariable: string
            myOtherGlobalVariable: My\Global\Type
```

### errorPatternsToIgnore
Type: `array`

List of patterns which can be found in compiled latte specific error message. These errors are ignored, and they are not sent back to phpstan.

Default:
```neon
parameters:
    latte:
        errorPatternsToIgnore: []
```

Example:
```neon
parameters:
    latte:
        errorPatternsToIgnore:
            - '/Unknown tag/'
```

### applicationMapping
Type: `array`

Application mapping should be the same as the mapping used in application. It is used for transforming links to correct method calls (`link SomePresenter:create` is transformed to `SomePresenter->actionCreate()` if mapping and method exists).

If not set link calls are not checked.

Default:
```neon
parameters:
    latte:
        applicationMapping: []
```

Example:
```neon
parameters:
    latte:
        applicationMapping:
            *: App\*\Presenters\*Presenter
            Foo: Foo\Bar\Presenters\*Presenter
```
