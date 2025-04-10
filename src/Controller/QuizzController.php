<?php

namespace App\Controller;

use App\Entity\Quiz;
use App\Entity\Answer;
use App\Entity\UserAnswer;
use App\Entity\Progression;
use App\Entity\QuizCompletion;
use Doctrine\ORM\EntityManager;
use App\Repository\ModuleViewRepository;
use App\Repository\UserAnswerRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\QuizCompletionRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class QuizzController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/quiz/{slug}/intro', name: 'quiz_intro')]
    public function intro(string $slug,  SessionInterface $session): Response
    {
        $quiz = $this->entityManager->getRepository(Quiz::class)->findOneBy(['slug' => $slug]);

        if (!$quiz) {
            throw $this->createNotFoundException('Le quiz demandé n\'existe pas.');
        }

        $session->set('quiz_intro_seen_' . $slug, true);

        return $this->render('quizz/intro.html.twig', [
            'quiz' => $quiz,
        ]);
    }

    #[Route('/quiz/{slug}', name: 'quiz_show')]
    public function show(string $slug,SessionInterface $session, Request $request, UserAnswerRepository $userAnswerRepository): Response
    {
            // Si l'utilisateur n'a pas visité l'intro, on le redirige
        if (!$session->get('quiz_intro_seen_' . $slug)) {
            return $this->redirectToRoute('quiz_intro', ['slug' => $slug]);
        }
        // Récupérer le quiz à partir du slug
        $quiz = $this->entityManager->getRepository(Quiz::class)->findOneBy(['slug' => $slug]);

        if (!$quiz) {
            throw $this->createNotFoundException('Le quiz n\'existe pas.');
        }

        // Créer un formulaire pour les réponses
        $formBuilder = $this->createFormBuilder();

        foreach ($quiz->getQuestions() as $question) {
        $formBuilder->add('answers_' . $question->getId(), ChoiceType::class, [
            'choices' => $question->getAnswers()->toArray(),
            'expanded' => true, // Render as radio buttons
            'multiple' => false, // Single selection
            'choice_label' => function (Answer $answer) {
                return $answer->getContent(); // Display the answer content
            },
            'choice_value' => function (?Answer $answer) {
                return $answer ? $answer->getId() : null; // Use the ID as the value
            },
            'label' => false, // Avoid displaying extra labels
        ]);

        }
        
        $form = $formBuilder->getForm();
        
        

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
    public function results(
        string $slug,
        UserAnswerRepository $userAnswerRepository,
        QuizCompletionRepository $quizCompletionRepository,
        ModuleViewRepository $moduleViewRepository,
        EntityManagerInterface $entityManager
    ): Response {
        // Récupérer le quiz
        $quiz = $this->entityManager->getRepository(Quiz::class)->findOneBy(['slug' => $slug]);
        if (!$quiz) {
            throw $this->createNotFoundException("Le quiz avec le slug '$slug' est introuvable.");
        }
    
        $user = $this->getUser();
        $formation = $quiz->getFormation();
        $questions = $quiz->getQuestions();
        $totalQuestions = count($questions);
        $score = 0;
    
        // Calcul du score
        $userAnswers = $userAnswerRepository->findByUserAndQuiz($user, $quiz);
        $score = 0;
        $totalPoints = 0;
        
        foreach ($quiz->getQuestions() as $question) {
            $totalPoints += $question->getPoints();
        }
        
        foreach ($userAnswers as $userAnswer) {
            if ($userAnswer->getAnswer()->isCorrect()) {
                $score += $userAnswer->getQuestion()->getPoints();
            }
        }
        
        // Enregistrer la complétion du quiz si non déjà faite
        $existingCompletion = $quizCompletionRepository->findOneBy([
            'user' => $user,
            'quiz' => $quiz,
            
        ]);
    
        if (!$existingCompletion) {
            $completion = new QuizCompletion();
            $completion->setUser($user);
            $completion->setQuiz($quiz);
            $entityManager->persist($completion);
        }
    
        // Récupération ou création de l'objet Progression
        $progression = $this->entityManager->getRepository(Progression::class)
            ->findOneBy(['user' => $user, 'formation' => $formation]);
    
        if (!$progression) {
            $progression = new Progression();
            $progression->setUser($user);
            $progression->setFormation($formation);
        }
    
        // Progression = (quizz terminés + modules vus) / total étapes
        $totalSteps = count($formation->getModules()) + count($formation->getQuizzes());
    
        $completedQuizzes = $quizCompletionRepository->findBy(['user' => $user]);
        $completedQuizzesInFormation = array_filter($completedQuizzes, function ($qc) use ($formation) {
            return $qc->getQuiz()->getFormation() === $formation;
        });
    
        $completedModules = $moduleViewRepository->findBy(['user' => $user]);
        $completedModulesInFormation = array_filter($completedModules, function ($mv) use ($formation) {
            return $mv->getModule()->getFormation() === $formation;
        });
    
        $completedCount = count($completedQuizzesInFormation) + count($completedModulesInFormation);
        $progressionPercent = $totalSteps > 0 ? ($completedCount / $totalSteps) * 100 : 0;
    
        $progression->setProgress(round($progressionPercent, 2));
    
        // Flush tout
        $this->entityManager->persist($progression);
        $this->entityManager->flush();
    
        return $this->render('quizz/results.html.twig', [
            'quiz' => $quiz,
            'score' => $score,
            'total' => $totalQuestions,
            'totalPoints' => $totalPoints,
        ]);
    }
    
    
}