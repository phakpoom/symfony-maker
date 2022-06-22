<?php

declare(strict_types=1);

namespace Bonn\Maker\Model;

use Bonn\Maker\Utils\DOMIndent;
use FluidXml\FluidContext;
use FluidXml\FluidXml;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

class SymfonyServiceXml
{
    private FluidXml $xml;

    public function __construct($file = null)
    {
        if (null !== $file) {
            if (!file_exists($file)) {
                throw new \InvalidArgumentException(sprintf('file xml %s not exists', $file));
            }

            $xml = FluidXml::load($file);
            $xml->namespace('container', XmlFileLoader::NS);
        } else {
            $xml = new FluidXml(null);

            $xml->add('container', null, [
                'xmlns' => 'http://symfony.com/schema/dic/services',
                'xmlns:xsi' => 'http://www.w3.org/2001/XMLSchema-instance',
                'xsi:schemaLocation' => 'http://symfony.com/schema/dic/services http://symfony.com/schema/dic/services/services-1.0.xsd',
            ]);

            $xml->namespace('container', XmlFileLoader::NS);

            $xml->query('/container')->add('services');
        }

        $this->xml = $xml;
    }

    public function hasImport($path): bool
    {
        $alreadyImported = false;
        $this->xml->query('//container:imports/container:import')->each(function ($index, \DOMElement $dom) use ($path, &$alreadyImported) {
            if (true === $alreadyImported) {
                return;
            }
            $alreadyImported = $path === $dom->getAttribute('resource');
        });

        return $alreadyImported;
    }

    public function addImport($path): void
    {
        if ($this->hasImport($path)) {
            return;
        }

        $importsContext = $this->xml->query('//container:imports', '//container/imports');

        if (0 === $importsContext->size()) {
            $importsContext = $this->xml->query('//container:services', '//container/services')->prepend('imports', true);
        }

        $importsContext->add('import', null, [
            'resource' => $path,
        ]);
    }

    public function addService(string $id, string $class, array $attrs = []): FluidContext
    {
        return $this->xml->query('//container/services', '//container:services')
            ->addChild('service', true, [
                'id' => $id,
                'class' => $class,
            ] + $attrs);
    }

    public function addPrototype(string $namespace, string $resource, array $attrs = []): void
    {
        if ($this->hasPrototype($namespace, $resource)) {
            return;
        }

        $this->xml->query('//container/services', '//container:services')
            ->addChild('prototype', true, [
                'namespace' => $namespace,
                'resource' => $resource,
            ] + $attrs);
    }

    public function hasPrototype(string $namespace, string $resource): bool
    {
        $already = false;
        $this->xml->query('//container:services/container:prototype')->each(function ($index, \DOMElement $dom) use ($namespace, $resource, &$already) {
            if (true === $already) {
                return;
            }

            $already = $namespace === $dom->getAttribute('namespace') && $resource === $dom->getAttribute('resource');
        });

        return $already;
    }

    public function getXml(): \FluidXml\FluidXml
    {
        return $this->xml;
    }

    public function __toString(): string
    {
        return (new DOMIndent($this->xml->__toString()))->saveXML();
    }
}
