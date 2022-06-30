# Symfony-maker
[![Build Status](https://travis-ci.org/phakpoom/symfony-maker.svg?branch=master)](https://travis-ci.org/phakpoom/symfony-maker)


## (LTS) master php8+
## (Drop Support) 1.0.0 php7.2+

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
```php
Bonn\Maker\Bridge\MakerBundle\BonnMakerBundle::class => ['dev' => true]
```

## Config
> Beware! make sure that config only dev
```yaml
bonn_maker:
    namespace_prefix: "App" # prefix namespace
    bundle_root_dir: "%kernel.project_dir%/src/App/" # use for list directories for generate class
    cache_dir: ~ # default cache_dir symfony
    cache_max_keep_versions: ~ # maximum keep class version for rollback
    writer_dev: ~ # if `true` it will echo on console instead create file
    // ... and many config about dir
```

## Usage

#### Generate model and doctrine mapping
`./bin/console bonn:model:maker --help`

#### Many Commands
`./bin/console bonn`
