<?php

declare(strict_types=1);

namespace Bonn\Maker\Generator\Sylius;

use Bonn\Maker\Model\Code;
use Bonn\Maker\Utils\NameResolver;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Yaml\Yaml;

interface SyliusResourceServiceNameResolverInterface
{
    /**
     * @param string $className
     *
     * @return string
     */
    public function getPrefix(string $className): string;

    /**
     * @param string $className
     *
     * @return string
     */
    public function getResourceName(string $className): string;

    /**
     * @param string $className
     *
     * @return string
     */
    public function getModelParameter(string $className): string;

    /**
     * @param string $className
     *
     * @return string
     */
    public function getController(string $className): string;

    /**
     * @param string $className
     *
     * @return string
     */
    public function getRepository(string $className): string;

    /**
     * @param string $className
     *
     * @return string
     */
    public function getFactory(string $className): string;

    /**
     * @param string $className
     *
     * @return string
     */
    public function getManager(string $className): string;
}
