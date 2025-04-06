<?php

namespace App\Form;

use App\Entity\Answer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class QuizQuestionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $question = $options['question'];
    
        $builder
            ->add('answer', EntityType::class, [
                'class' => Answer::class,
                'choices' => $question->getAnswers(),
                'expanded' => true,
                'multiple' => false,
                'label' => false,
                'choice_label' => 'content',
            ]);
    }
    
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([]);
        $resolver->setRequired('question');
    }
    
}
