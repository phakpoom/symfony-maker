<?php

declare(strict_types=1);

namespace Bonn\Maker\Generator;

use Bonn\Maker\Model\Code;
use Bonn\Maker\Utils\NameResolver;
use Bonn\Maker\Utils\PhpDoctypeCode;
use Nette\PhpGenerator\PhpNamespace;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class ValidatorGenerator extends AbstractGenerator implements GeneratorInterface
{
    /**
     * {@inheritdoc}
     */
    public function configurationOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'validator_service_file_path' => null,
                'all_service_file_path' => null,
                'config_dir' => null,
            ])
            ->setRequired('name')
            ->setRequired('namespace')
            ->setRequired('validator_dir')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function generateWithResolvedOptions(array $options)
    {
        $resourcePrefix = NameResolver::resolveResourcePrefix($options['namespace']);

        $fileLocateConstraint = NameResolver::replaceDoubleSlash($options['validator_dir'] . '/' . $options['name'] . '.php');
        $fileLocateConstraintValidator = NameResolver::replaceDoubleSlash($options['validator_dir'] . '/' . $options['name'] . 'Validator.php');

        $classNamespace = $this->generateConstraint(new PhpNamespace($options['namespace']), $options);
        $classNamespaceConstraintValidator = $this->generateConstraintValidator(new PhpNamespace($options['namespace']), $options);

        $this->manager->persist(new Code(PhpDoctypeCode::render($classNamespace->__toString()), $fileLocateConstraint));
        $this->manager->persist(new Code(PhpDoctypeCode::render($classNamespaceConstraintValidator->__toString()), $fileLocateConstraintValidator));

        if (null === $options['validator_service_file_path'] || null === $options['all_service_file_path'] || null === $options['config_dir']) {
            return;
        }

        // import service form
        $this->addImportEntryToServiceFile(
            $options['config_dir'],
            $options['validator_service_file_path'],
            $options['all_service_file_path']
        );

        $xml = $this->getConfigXmlFile($options['config_dir'], $options['validator_service_file_path']);

        $resourceName =  NameResolver::camelToUnderScore($options['name']);
        $serviceContext = $xml->addService(
            sprintf('%s.validator.%s_validator', $resourcePrefix, $resourceName),
            $classNamespace->getName() . '\\' . $classNamespaceConstraintValidator->getClasses()['DummyValidator']->getName(),
            ['autowire' => 'true', 'public' => 'true']
        );

        $serviceContext->addChild('tag', null, [
            'name' => 'validator.constraint_validator',
            'alias' => $resourceName . '_validator'
        ]);

        $this->manager->persist(new Code($xml->__toString(), $options['config_dir'] . $options['validator_service_file_path']));
    }

    private function generateConstraint(PhpNamespace $classNamespace, array $options): PhpNamespace
    {
        $classNamespace->addUse(Constraint::class);

        $class = $classNamespace->addClass($options['name']);
        $class->addExtend(Constraint::class);
        $method = $class->addMethod('getTargets');
        $method->setBody(<<<PHP
// or change to prop
return self::CLASS_CONSTRAINT;
PHP
        );

        $method = $class->addMethod('validatedBy');
        $name = NameResolver::camelToUnderScore($options['name']);
        $method->setBody(<<<PHP
return '{$name}_validator';
PHP
        );

        return $classNamespace;
    }

    private function generateConstraintValidator(PhpNamespace $classNamespace, array $options): PhpNamespace
    {
        $classNamespace->addUse(Constraint::class);
        $classNamespace->addUse(ConstraintValidator::class);

        $class = $classNamespace->addClass($options['name'] . 'Validator');
        $class->addExtend(ConstraintValidator::class);
        $method = $class->addMethod('validate');
        $method->setComment("\n@param \$value\n@param Constraint \$constraint\n");
        $method->addParameter('value');
        $method->addParameter('constraint')->setTypeHint(Constraint::class);
        $method->setBody(<<<PHP
// do stuff
PHP
        );

        return $classNamespace;
    }
}
