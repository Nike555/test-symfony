<?php

namespace App\Form;

use App\Entity\UserGamePrize;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserPlayGameFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /*$builder
            ->add('user')
            ->add('game')
            ->add('prize')
        ;*/
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UserGamePrize::class,
        ]);
    }
}
