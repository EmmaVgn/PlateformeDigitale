<?php

namespace App\Controller;

use App\Repository\ProgressionRepository;
use App\Repository\UserFormationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\QuizCompletionRepository;
use App\Repository\UserAnswerRepository;
use App\Entity\Quiz;

final class DashboardController extends AbstractController
{
    #[Route('/profile', name: 'profile')]
    public function index(
        UserFormationRepository $userFormationRepository,
        ProgressionRepository $progressionRepository,
        QuizCompletionRepository $quizCompletionRepository,
        UserAnswerRepository $userAnswerRepository
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $user = $this->getUser();
    
        $inscriptions = $userFormationRepository->findBy(['user' => $user]);
        $progressions = $progressionRepository->findBy(['user' => $user]);
    
        $progressionsByFormationId = [];
        foreach ($progressions as $progression) {
            $progressionsByFormationId[$progression->getFormation()->getId()] = $progression->getProgress();
        }
    
        // ✅ Récupérer les complétions de quiz de l'utilisateur
        $quizCompletions = $quizCompletionRepository->findBy(['user' => $user]);
        $quizResults = [];
    
        foreach ($quizCompletions as $completion) {
            $quiz = $completion->getQuiz();
            $questions = $quiz->getQuestions();
            $totalPoints = 0;
            $score = 0;
    
            foreach ($questions as $question) {
                $totalPoints += $question->getPoints();
            }
    
            $userAnswers = $userAnswerRepository->findByUserAndQuiz($user, $quiz);
    
            foreach ($userAnswers as $userAnswer) {
                if ($userAnswer->getAnswer()->isCorrect()) {
                    $score += $userAnswer->getQuestion()->getPoints();
                }
            }
    
            $quizResults[] = [
                'quiz' => $quiz,
                'score' => $score,
                'total' => $totalPoints,
            ];
        }
    
        return $this->render('dashboard/index.html.twig', [
            'inscriptions' => $inscriptions,
            'progressions' => $progressionsByFormationId,
            'quizResults' => $quizResults,
        ]);
    }
}