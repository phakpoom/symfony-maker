<?php

declare(strict_types=1);

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\GreaterThan;

final class SimpleFormType extends AbstractResourceType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options = []): void
    {
        $builder
            ->add('code', TextType::class, [])
            ->add('name', TextType::class, [
                'label' => 'bonn.ui.form.name',
            ])
            ->add('min', IntegerType::class, [
                'required' => false,
                'label' => "bonn.ui.form.min",
                'constraints' => [
                    new GreaterThan([
                        'value' => 0,
                        'groups' => $options['validation_groups'],
                    ])
                ]
            ])
            ->add('max', IntegerType::class, [
                'required' => false,
                'label' => 'bonn.ui.form.max',
                'constraints' => [
                    new GreaterThan([
                        'value' => 0,
                        'groups' => $options['validation_groups'],
                    ])
                ]
            ])
            ->add('color', ColorType::class, [
                'label' => 'sylius.form.color'
            ]);
    }
}
