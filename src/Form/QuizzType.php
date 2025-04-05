<?php
namespace App\Form;

use App\Entity\Question;
use App\Entity\Quiz;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class QuizzType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $quiz = $options['quiz']; // On récupère le quiz lié à ce formulaire

        foreach ($quiz->getQuestions() as $question) {
            $choices = [];
            foreach ($question->getResponses() as $response) {
                // Assurez-vous que chaque réponse est unique
                if (!isset($choices[$response->getContent()])) {
                    $choices[$response->getContent()] = $response->getId();
                }
            }
        
            $builder->add('question_' . $question->getId(), ChoiceType::class, [
                'label' => $question->getContent(),
                'choices' => $choices, // Réponses uniques
                'expanded' => true, 
                'multiple' => false,
            ]);
        }
        

        $builder->add('submit', SubmitType::class, [
            'label' => 'Soumettre'
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Quiz::class,
            'quiz' => null, // L'option quiz est ajoutée pour lier un quiz spécifique au formulaire
        ]);
    }
}