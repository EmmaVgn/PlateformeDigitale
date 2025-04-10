<?php

namespace App\Controller;

use App\Entity\UserFormation;
use App\Repository\FormationRepository;
use App\Repository\ModuleViewRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ProgressionRepository;
use App\Repository\QuizCompletionRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class FormationController extends AbstractController
{
    #[Route('/formations', name: 'app_formations')]
    public function index(FormationRepository $formationRepository, Request $request, PaginatorInterface $paginator): Response
    {
        $queryBuilder = $formationRepository->createQueryBuilder('f')
            ->where('f.isPublished = true');
    
        if ($request->query->get('q')) {
            $queryBuilder
                ->andWhere('f.title LIKE :q OR f.description LIKE :q')
                ->setParameter('q', '%' . $request->query->get('q') . '%');
        }
    
        $formations = $paginator->paginate(
            $queryBuilder->getQuery(),
            $request->query->getInt('page', 1),
            6
        );
    
        return $this->render('formation/index.html.twig', [
            'formations' => $formations,
        ]);
    }
    

    #[Route('/formation/{slug}', name: 'app_formation_show')]
    public function show(string $slug, FormationRepository $formationRepository, ProgressionRepository $progressionRepository, ModuleViewRepository $moduleViewRepository, QuizCompletionRepository $quizCompletionRepository): Response
    {
        $formation = $formationRepository->findOneBy(['slug' => $slug]);
        

        if (!$formation || !$formation->isPublished()) {
            throw $this->createNotFoundException('Formation introuvable.');
        }

        $inscriptionValide = false;
        $inscription = null;

        if ($this->getUser()) {
            foreach ($formation->getInscriptions() as $item) {
                if ($item->getUser() === $this->getUser()) {
                    $inscription = $item;
                    if ($item->getisValidated()) {
                        $inscriptionValide = true;
                    }
                    break;
                }
            }
        }

        $progression = $progressionRepository->findOneBy([
            'user' => $this->getUser(),
            'formation' => $formation
        ]);

        $progress = $progression ? $progression->getProgress() : 0;

        
        $modules = $formation->getModules();
        $totalMinutes = 0;
        $userMinutes = 0;
    
        foreach ($modules as $module) {
            $moduleViewed = $moduleViewRepository->findOneBy([
                'user' => $this->getUser(),
                'module' => $module
            ]);
        
            foreach ($modules as $module) {
                $moduleViewed = $moduleViewRepository->findOneBy([
                    'user' => $this->getUser(),
                    'module' => $module
                ]);
            
                foreach ($module->getPdfs() as $pdf) {
                    $duration = $pdf->getEstimatedDuration() ?? 0;
                    $totalMinutes += $duration;
            
                    if ($moduleViewed) {
                        $userMinutes += $duration;
                    }
                }
            }
            
        }
        
        $quizCompletions = $quizCompletionRepository->findBy(['user' => $this->getUser()]);
        $completedQuizIds = array_map(fn($qc) => $qc->getQuiz()->getId(), $quizCompletions);
        
        foreach ($formation->getQuizzes() as $quiz) {
            $duration = $quiz->getEstimatedDuration() ?? 0;
            $totalMinutes += $duration;
        
            if (in_array($quiz->getId(), $completedQuizIds)) {
                $userMinutes += $duration;
            }
        }
        
    
        $remainingMinutes = max(0, $totalMinutes - $userMinutes);
     


        return $this->render('formation/show.html.twig', [
            'formation' => $formation,
            'inscription' => $inscription,
            'inscriptionValide' => $inscriptionValide,
            'progress' => $progress,
            'modules' => $modules,
            'totalDuration' => $totalMinutes,
            'remainingDuration' => $remainingMinutes,
        ]);
    }

    #[Route('/formations/{slug}/inscription', name: 'formation_inscription')]
    public function inscription(
        string $slug,
        FormationRepository $formationRepository,
        EntityManagerInterface $em
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_USER');
    
        $formation = $formationRepository->findOnePublishedBySlug($slug);
    
        if (!$formation) {
            throw $this->createNotFoundException('Formation non trouvée.');
        }
    
        $user = $this->getUser();
    
        // Vérifie si l’utilisateur est déjà inscrit
        foreach ($formation->getInscriptions() as $inscription) {
            if ($inscription->getUser() === $user) {
                $this->addFlash('warning', 'Vous êtes déjà inscrit à cette formation.');
                return $this->redirectToRoute('formation_show', ['slug' => $slug]);
            }
        }
    
        $inscription = new UserFormation();
        $inscription->setUser($user);
        $inscription->setFormation($formation);

        $inscription->setIsCompleted(false);
    
        $em->persist($inscription);
        $em->flush();
    
        $this->addFlash('success', 'Vous êtes maintenant inscrit à cette formation.');
    
        return $this->redirectToRoute('formation_show', ['slug' => $slug]);
    }


}

