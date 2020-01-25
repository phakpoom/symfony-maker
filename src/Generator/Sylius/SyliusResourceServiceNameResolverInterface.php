<?php

declare(strict_types=1);

namespace Bonn\Maker\Generator\Sylius;

interface SyliusResourceServiceNameResolverInterface
{
    public function getPrefix(string $className): string;

    public function getResourceName(string $className): string;

    public function getModelParameter(string $className): string;

    public function getController(string $className): string;

    public function getRepository(string $className): string;

    public function getFactory(string $className): string;

    public function getManager(string $className): string;
}
