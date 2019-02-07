# Symfony-maker
[![Build Status](https://travis-ci.org/phakpoom/symfony-maker.svg?branch=master)](https://travis-ci.org/phakpoom/symfony-maker)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/phakpoom/symfony-maker/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/phakpoom/symfony-maker/?branch=master)

## Installation with composer

```json
{
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/phakpoom/symfony-maker.git"
        }
    ]
},
"require-dev": {
    "phakpoom/symfony-maker": "dev-master",
},
```

## Enabled Bundle
For symfony 3.4.x
```php
if ('dev' === $this->getEnvironment()) {
    new Bonn\Maker\Bridge\MakerBundle\BonnMakerBundle();
}
```
For symfony 4
```php
Bonn\Maker\Bridge\MakerBundle\BonnMakerBundle::class => ['dev' => true]
```

## Config
> Beware! make sure that config only dev `config_dev.yml`
```yaml
bonn_maker:
    namespace_prefix: "App" # prefix namespace
    bundle_root_dir: "%kernel.project_dir%/src/App/" # use for list directories for generate class
    #model_dir_name: ~ # default Model
    #doctrine_mapping_dir: ~ # default Resources/config/doctrine/model
    cache_dir: ~ # default cache_dir symfony
    cache_max_keep_versions: ~ # maximum keep class version for rollback
    writer_dev: ~ # if `true` it will echo on console instead create file
```

## Usage

#### Generate model and doctrine mapping
`./bin/console bonn:model:maker --help`
