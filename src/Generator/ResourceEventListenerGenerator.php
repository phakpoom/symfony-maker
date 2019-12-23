<?php

declare(strict_types=1);

namespace Bonn\Maker\Generator;

use Bonn\Maker\Model\Code;
use Bonn\Maker\Utils\NameResolver;
use Bonn\Maker\Utils\PhpDoctypeCode;
use Nette\PhpGenerator\PhpNamespace;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ResourceEventListenerGenerator extends AbstractGenerator implements GeneratorInterface
{
    /**
     * {@inheritdoc}
     */
    public function configurationOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'all_service_file_path' => null,
                'entry_service_file_path' => null,
                'config_dir' => null,
            ])
            ->setRequired('name')
            ->setRequired('namespace')
            ->setRequired('class_dir');
    }

    /**
     * {@inheritdoc}
     */
    protected function generateWithResolvedOptions(array $options)
    {
        $fileLocate = NameResolver::replaceDoubleSlash($options['class_dir'] . '/' . $options['name'] . 'Listener.php');
        $resourcePrefix = NameResolver::resolveResourcePrefix($options['namespace']);

        $classNamespace = new PhpNamespace($options['namespace']);
        $classNamespace->addUse(ResourceControllerEvent::class);

        $class = $classNamespace->addClass($options['name'] . 'Listener')->setFinal();

        $method = $class->addMethod('__construct');

        $method = $class->addMethod('methodName');
        $method->addParameter('event')->setTypeHint(ResourceControllerEvent::class);

        $this->manager->persist(new Code(PhpDoctypeCode::render($classNamespace->__toString()), $fileLocate));

        if (null === $options['entry_service_file_path'] || null === $options['all_service_file_path'] || null === $options['config_dir']) {
            return;
        }

        // import service form
        $this->addImportEntryToServiceFile($options['config_dir'], $options['entry_service_file_path'], $options['all_service_file_path']);

        $xml = $this->getConfigXmlFile($options['config_dir'], $options['entry_service_file_path']);

        $resourceName = NameResolver::camelToUnderScore($options['name']);
        $serviceContext = $xml->addService(
            sprintf('%s.event_listener.%s_resource_listener', $resourcePrefix, $resourceName),
            $classNamespace->getName() . '\\' . $class->getName(),
            ['autowire' => 'true']
        );

        $serviceContext->addChild('tag', null, [
            'name' => 'kernel.event_listener',
            'event' =>  sprintf('%s.%s.%s', $resourcePrefix, '{{resource_name}}', '{{pre|post}}_{{create|update|delete}}'),
            'method' => '{{methodName}}',
        ]);

        $this->manager->persist(new Code($xml->__toString(), $options['config_dir'] . $options['entry_service_file_path']));
    }
}
