# Toujou API

@todo:
- Fix runTests.sh as no typo3conf is set created in .Build/

## Installation

Require and install the plugin

    $ composer require toujou/{{lowercase_projectname}}
    $ vendor/bin/typo3cms extension:install toujou_api

## Development

Install php dependencies using composer:

    $ composer install

#### [PHPUnit](https://phpunit.de) Unit tests

    $ etc/scripts/runTests.sh

#### [PHPUnit](https://phpunit.de) Functional tests

    $ etc/scripts/runTests.sh -s functional


#### [Easy-Coding-Standard](https://github.com/Symplify/EasyCodingStandard)

Check coding standard violations

    $ etc/scripts/checkCodingStandards.sh

Fix coding standard violations automatically

    $ etc/scripts/checkCodingStandards.sh --fix


## Documentation

Make `dockrun_t3rd available in current terminal

    source <(docker run --rm t3docs/render-documentation show-shell-commands)

Run `dockrun_t3rd`

    dockrun_t3rd makehtml

Open documentation

    open "Documentation-GENERATED-temp/Result/project/0.0.0/Index.html"