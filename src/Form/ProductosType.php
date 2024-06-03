<?php

// src/Form/ProductosType.php
namespace App\Form;

use App\Entity\Productos;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductosType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nombre', TextType::class, [
                'label' => 'Nombre',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('descripcion', TextareaType::class, [
                'label' => 'Descripción',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('precio', MoneyType::class, [
                'label' => 'Precio',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('stock', IntegerType::class, [
                'label' => 'Stock',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('categoria', TextType::class, [
                'label' => 'Categoría',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('imagen', TextType::class, [
                'label' => 'Imagen',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('tipo_iva', ChoiceType::class, [
                'label' => 'Tipo de IVA',
                'choices'  => [
                    '0%' => 0,
                    '10%' => 10,
                    '21%' => 21,
                ],
                'attr' => ['class' => 'form-control'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Productos::class,
        ]);
    }
}
