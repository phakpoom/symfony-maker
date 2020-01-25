<?php

declare(strict_types=1);

namespace Bonn\Maker\Generator;

use Bonn\Maker\Manager\CodeManagerInterface;
use Bonn\Maker\Model\Code;
use Bonn\Maker\Model\SymfonyServiceXml;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class AbstractGenerator implements GeneratorInterface
{
    /** @var CodeManagerInterface */
    protected $manager;

    public function setManager(CodeManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @return mixed
     */
    abstract protected function generateWithResolvedOptions(array $options);

    public function configurationOptions(OptionsResolver $resolver)
    {
        // setting option
    }

    public function generate($options = [])
    {
        $optionResolver = new OptionsResolver();
        $this->configurationOptions($optionResolver);

        $options = $optionResolver->resolve($options);

        $this->generateWithResolvedOptions($options);
    }

    /**
     * Add entry file `twigs.xml` to `services.xml`
     */
    protected function addImportEntryToServiceFile(string $configDir, string $entryName, string $allServiceFile): SymfonyServiceXml
    {
        $allServicePath = $configDir . $allServiceFile;
        // import service form
        if (!file_exists($allServicePath)) {
            throw new \InvalidArgumentException($allServicePath . ' is not a file');
        }

        $serviceXml = new SymfonyServiceXml($allServicePath);

        $entryFile = ltrim($entryName, '/');

        if (!$serviceXml->hasImport($entryFile)) {
            $serviceXml->addImport($entryFile);
            $this->manager->persist(new Code($serviceXml->__toString(), $allServicePath));
        }

        return $serviceXml;
    }

    /**
     * Create Config Xml
     */
    protected function getConfigXmlFile(string $configDir, string $configFile): SymfonyServiceXml
    {
        if (file_exists($configDir . $configFile)) {
            return new SymfonyServiceXml($configDir . $configFile);
        }

        return new SymfonyServiceXml();
    }

    protected function ensureClassExists(string $class)
    {
        if (!class_exists($class)) {
            throw new \InvalidArgumentException(sprintf('Class %s do not exists', $class));
        }
    }
}
