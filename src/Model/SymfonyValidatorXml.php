<?php

declare(strict_types=1);

namespace Bonn\Maker\Model;

use Bonn\Maker\Utils\DOMIndent;
use FluidXml\FluidXml;
use Symfony\Component\Validator\Mapping\Loader\XmlFileLoader;

class SymfonyValidatorXml
{
    /** @var FluidXml */
    private $xml;

    /**
     * @param null $file
     */
    public function __construct($file = null)
    {
        if (null !== $file) {
            if (!file_exists($file)) {
                throw new \InvalidArgumentException(sprintf('file xml %s not exists', $file));
            }

            $xml = FluidXml::load($file);
            $xml->namespace('constraint-mapping', 'http://symfony.com/schema/dic/constraint-mapping');
        } else {
            $xml = new FluidXml(null);

            $xml->add('constraint-mapping', null, [
                'xmlns' => 'http://symfony.com/schema/dic/constraint-mapping',
                'xmlns:xsi' => 'http://www.w3.org/2001/XMLSchema-instance',
                'xsi:schemaLocation' => 'http://symfony.com/schema/dic/constraint-mapping http://symfony.com/schema/dic/services/constraint-mapping-1.0.xsd',
            ]);

            $xml->namespace('constraint-mapping', 'http://symfony.com/schema/dic/constraint-mapping');
        }

        $this->xml = $xml;
    }

    public function getXml()
    {
        return $this->xml;
    }

    public function __toString(): string
    {
        return (new DOMIndent($this->xml->__toString()))->saveXML();
    }
}
