<?php

namespace App\Form;

use App\Entity\MessageContact;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;


class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('name', TextType::class, [
            'label' => 'Nom',
            'attr' => ['placeholder' => 'Votre nom'],
            'constraints' => [new NotBlank(['message' => 'Ce champ est obligatoire.'])],
        ])
        ->add('email', EmailType::class, [
            'label' => 'Email',
            'attr' => ['placeholder' => 'Votre adresse email'],
            'constraints' => [new NotBlank(['message' => 'Ce champ est obligatoire.'])],
        ])
        ->add('message', TextareaType::class, [
            'label' => 'Message',
            'attr' => [
                'placeholder' => 'Écrivez votre message ici...',
                'rows' => 6
            ],
            'constraints' => [new NotBlank(['message' => 'Merci de renseigner votre message.'])],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => MessageContact::class,
        ]);
    }
}
