<?php

declare(strict_types=1);

namespace Bonn\Maker\Generator;

use Bonn\Maker\Model\Code;
use Bonn\Maker\Model\SymfonyValidatorXml;
use Bonn\Maker\Utils\NameResolver;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ValidatorConfigGenerator extends AbstractGenerator implements GeneratorInterface
{
    /**
     * {@inheritdoc}
     */
    public function configurationOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired('full_class_name')
            ->setRequired('validator_config_dir');
    }

    /**
     * {@inheritdoc}
     */
    protected function generateWithResolvedOptions(array $options)
    {
        $this->ensureClassExists($options['full_class_name']);

        $className = NameResolver::resolveOnlyClassName($options['full_class_name']);
        $fileLocate = NameResolver::replaceDoubleSlash($options['validator_config_dir'] . '/' . $className . '.xml');

        if (file_exists($fileLocate)) {
            throw new \LogicException('Config file already exists.');
        }

        $xml = new SymfonyValidatorXml();
        $classXml = $xml->getXml()->query('/constraint-mapping')
            ->add('class', true, [
                'name' => $options['full_class_name']
            ])
        ;
        //
        $classXml
            ->add('constraint', true, [
                'name' => UniqueEntity::class
            ])
            ->addChild('option', 'code', [
                'name' => 'fields'
            ])
            ->addChild('option', 'app', [
                'name' => 'groups'
            ])
        ;

        $classXml
            ->add('constraint', true, [
                'name' => "{{VALIDATOR_CLASS_NAME}}"
            ])
            ->addChild('option', 'app', [
                'name' => 'groups'
            ])
        ;

        $this->manager->persist(new Code($xml->__toString(), $fileLocate));
    }
}
