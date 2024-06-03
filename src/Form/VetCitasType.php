<?php

namespace App\Form;

use App\Entity\Citas;
use App\Entity\Tiendas;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VetCitasType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('fecha', null, [
            'widget' => 'single_text',
        ])
        ->add('servicio', ChoiceType::class, [
            'label' => 'Seleccione el servicio',
            'choices' => [
                'Perro' => 'vet-perro',
                'Gato' => 'vet-gato',
            ],
            'required' => true,
            'attr' => [
                'class' => 'form-control',
                'id' => 'servicio',
            ],
            'placeholder' => 'Seleccione el servicio',
        ])
            ->add('cliente', null, [
                'attr' => ['placeholder' => 'Nombre']
        ])
            ->add('telefono', null, [
                'attr' => ['placeholder' => 'Teléfono']
        ])
            ->add('horas', ChoiceType::class, [
                'label' => 'Seleccione la hora',
                'choices' => [
                    '9:30 - 10:45' => '9:30-10:45',
                    '10:45 - 12:00' => '10:45-12:00',
                    '12:00 - 13:15' => '12:00-13:15',
                    '13:15 - 14:30' => '13:15-14:30',
                    '16:30 - 17:45' => '16:30-17:45',
                    '17:45 - 19:00' => '17:45-19:00',
                    '19:00 - 20:15' => '19:00-20:15',
                ],
                'required' => true,
                'placeholder' => 'Seleccione la hora',
                'attr' => [
                    'class' => 'form-control',
                    'id' => 'hora',
                ],
            ])
            ->add('descripcion', null, [
                'label' => 'Indiqueme la raza y peso de su peludo.',
                'attr' => [
                    'class' => 'form-control textarea-focus',
                    'id' => 'message',
                    'placeholder' => 'Indiqueme la raza y peso de su peludo.',
                    'rows' => 4,
                    'required' => true,
                ],
            ])
            ->add('tienda', EntityType::class, [
                'class' => Tiendas::class,
                'choice_label' => 'nombre',
                'placeholder' => 'Seleccione una tienda',
                'required' => true,
            ]);
    
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Citas::class,
        ]);
    }
}
