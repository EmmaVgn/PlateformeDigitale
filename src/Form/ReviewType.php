<?php
namespace App\Form;

use App\Entity\User;
use App\Entity\Review;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ReviewType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('rating', HiddenType::class, [
                'label' => 'Note sur 5', // Ce label peut être supprimé si vous voulez un champ totalement caché
                'data' => 1, // Valeur initiale de la note, à modifier au clic sur les étoiles
                'mapped' => true, // S'assurer que ce champ est relié à l'entité
            ])
            ->add('comment', TextareaType::class, [
                'label' => 'Commentaire',
                'attr' => [
                    'rows' => 5,
                    'placeholder' => 'Votre retour d’expérience…'
                ]
            ])
            ->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'firstname',
                'disabled' => true,
            ]);
            
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Review::class,
        ]);
    }
}
