<?php

namespace App\Form;

use App\Entity\User;
use Doctrine\DBAL\Types\IntegerType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email')
            ->add('password')
            ->add('isVerified')
            ->add('status', ChoiceType::class,[
                'choices' => [
                    'Disponible'=> '1',
                    'Indisponible' => '0'
                ]
            ])
            ->add('category', ChoiceType::class,[
                'choices' =>[
                    'Je propose de l\'aide' => '1',
                    'J\'ai besoin d\'aide' =>  '0',
                ]
            ])
            ->add('help')
            ->add('phone')
            ->add('avatar')
            ->add('buildingPassword')
            ->add('username')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
