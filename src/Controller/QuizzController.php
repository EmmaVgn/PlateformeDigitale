<?php

namespace App\Controller;

use App\Entity\Quiz;
use App\Entity\Answer;
use App\Entity\UserAnswer;
use App\Repository\UserAnswerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class QuizzController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/quiz/{slug}', name: 'quiz_show')]
    public function show(string $slug, Request $request, UserAnswerRepository $userAnswerRepository): Response
    {
        // Récupérer le quiz à partir du slug
        $quiz = $this->entityManager->getRepository(Quiz::class)->findOneBy(['slug' => $slug]);

        if (!$quiz) {
            throw $this->createNotFoundException('Le quiz n\'existe pas.');
        }

        // Créer un formulaire pour les réponses
        $formBuilder = $this->createFormBuilder();

        // Ajouter des cases à cocher pour chaque question
        foreach ($quiz->getQuestions() as $question) {
            $formBuilder->add('answers_' . $question->getId(), ChoiceType::class, [
                'choices' => $question->getAnswers()->toArray(),
                'expanded' => true,
                'multiple' => false,
                'choice_label' => function (Answer $answer) {
                    return $answer->getContent(); // Afficher le contenu de chaque réponse
                },
                'choice_value' => function (?Answer $answer) {
                    return $answer ? $answer->getId() : null; // Utiliser l'ID de la réponse comme valeur
                },
                'label' => $question->getContent(), // Afficher la question comme label
            ]);
        }

        // Ajouter un bouton de soumission
        $formBuilder->add('submit', SubmitType::class, ['label' => 'Soumettre mes réponses']);
        $form = $formBuilder->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $userAnswersExistantes = $userAnswerRepository->findByUserAndQuiz($this->getUser(), $quiz);
            foreach ($userAnswersExistantes as $oldAnswer) {
                $this->entityManager->remove($oldAnswer);
            }

            foreach ($quiz->getQuestions() as $question) {
                $userAnswer = new UserAnswer();
                $userAnswer->setUser($this->getUser());
                $userAnswer->setQuiz($quiz);
        
                // Récupérer l'identifiant de la réponse sélectionnée
                $answerId = $data['answers_' . $question->getId()] ?? null;
        
                if (!$answerId) {
                    throw new \Exception('Une réponse doit être sélectionnée pour chaque question.');
                }
        
                // Récupérer l'objet Answer avec cet identifiant
                $answer = $this->entityManager->getRepository(Answer::class)->find($answerId);

                if (!$answer) {
                    throw new \Exception("La réponse avec l'ID $answerId est introuvable dans la base de données.");
                }
                
                $userAnswer->setQuestion($question);
                $userAnswer->setAnswer($answer);
                $this->entityManager->persist($userAnswer);
            }
        
            $this->entityManager->flush();
        
            return $this->redirectToRoute('quiz_results', ['slug' => $quiz->getSlug()]);
        }

        return $this->render('quizz/show.html.twig', [
            'quiz' => $quiz,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/quiz/{slug}/results', name: 'quiz_results')]
    public function results(string $slug, UserAnswerRepository $userAnswerRepository): Response
    {
        // Récupérer le Quiz à partir du slug
        $quiz = $this->entityManager->getRepository(Quiz::class)->findOneBy(['slug' => $slug]);
    
        if (!$quiz) {
            throw $this->createNotFoundException("Le quiz avec le slug '$slug' est introuvable.");
        }
    
        // Récupérer les réponses de l'utilisateur pour ce quiz
        $userAnswers = $userAnswerRepository->findByUserAndQuiz($this->getUser(), $quiz);

        $questions = $quiz->getQuestions();
        $totalQuestions = count($questions);
        $score = 0;
        
        foreach ($userAnswers as $userAnswer) {
            if ($userAnswer->getAnswer()->isCorrect()) {
                $score++;
            }
        }
        
        return $this->render('quizz/results.html.twig', [
            'quiz' => $quiz,
            'score' => $score,
            'total' => $totalQuestions
        ]);
    }
    
}
