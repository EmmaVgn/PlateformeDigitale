<?php

namespace App\Form;

use App\Entity\FeedbackFormation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Component\Validator\Constraints\NotBlank;

class FeedbackFormationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('noteContenu', IntegerType::class, [
                'label' => 'Note sur le contenu (1 à 5)',
                'constraints' => [
                    new NotBlank(),
                    new Range(['min' => 1, 'max' => 5]),
                ],
            ])
            ->add('noteSupport', IntegerType::class, [
                'label' => 'Note sur les supports (1 à 5)',
                'constraints' => [
                    new NotBlank(),
                    new Range(['min' => 1, 'max' => 5]),
                ],
            ])
            ->add('commentaireLibre', TextareaType::class, [
                'label' => 'Votre commentaire (optionnel)',
                'required' => false,
                'attr' => ['rows' => 5]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => FeedbackFormation::class,
        ]);
    }
}
