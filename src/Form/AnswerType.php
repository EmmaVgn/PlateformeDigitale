<?php

namespace App\Form;

use App\Entity\Answer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormInterface;


class AnswerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('content', TextType::class, [
                'label' => 'Content',
                'required' => true,
            ])
            ->add('isCorrect', CheckboxType::class, [
                'label' => 'Is this answer correct?',
                'required' => false,
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Save Answer',
            ]);
    }
}