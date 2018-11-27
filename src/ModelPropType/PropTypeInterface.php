<?php

namespace Bonn\Maker\ModelPropType;

use Bonn\Maker\Manager\CodeManagerInterface;
use Nette\PhpGenerator\ClassType;

interface PropTypeInterface
{
    /**
     * @return string
     */
    public static function getTypeName(): string;

    /**
     * @param ClassType $classType
     * @return mixed
     */
    public function addProperty(ClassType $classType);

    /**
     * @param ClassType $classType
     * @return mixed
     */
    public function addGetter(ClassType $classType);

    /**
     * @param ClassType $classType
     * @return mixed
     */
    public function addSetter(ClassType $classType);

    /**
     * @param string $className
     * @param \SimpleXMLElement $XMLElement
     * @param CodeManagerInterface $codeManager
     *
     * @return mixed
     */
    public function addDoctrineMapping(string $className, \SimpleXMLElement $XMLElement, CodeManagerInterface $codeManager);
}