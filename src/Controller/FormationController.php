<?php

namespace App\Controller;

use App\Entity\Review;
use App\Form\ReviewType;
use App\Entity\UserFormation;
use App\Service\SendMailService;
use App\Entity\DemandeEntreprise;
use App\Service\FormationService;
use Symfony\Component\Mime\Email;
use App\Form\ContactEntrepriseType;
use App\Repository\ReviewRepository;
use App\Repository\CategorieRepository;
use App\Repository\FormationRepository;
use App\Repository\ModuleViewRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ProgressionRepository;
use App\Repository\UserFormationRepository;
use Knp\Component\Pager\PaginatorInterface;
use App\Repository\QuizCompletionRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
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
    public function show(
        string $slug, 
        FormationRepository $formationRepository, 
        ProgressionRepository $progressionRepository, 
        ModuleViewRepository $moduleViewRepository, 
        QuizCompletionRepository $quizCompletionRepository,     
        Request $request,   
        ReviewRepository $reviewRepository,
        EntityManagerInterface $entityManager,
        UserFormationRepository $userFormationRepository,
        SendMailService $mail
    ): Response {
        $formation = $formationRepository->findOneBy(['slug' => $slug]);
        
        if (!$formation || !$formation->isPublished()) {
            throw $this->createNotFoundException('Formation introuvable.');
        }
    
        // Vérifier si l'utilisateur est connecté
        $user = $this->getUser();
        $isEnrolled = false;
        $hasReviewed = false;
        $existingReview = null; // Définir à null

        // Vérifier si l'utilisateur est inscrit à cette formation et validé
        $inscriptionValide = false;
        $inscription = null;
        if ($this->getUser()) {
            foreach ($formation->getInscriptions() as $item) {
                if ($item->getUser() === $this->getUser()) {
                    $inscription = $item;
                    if ($item->getIsValidated()) {
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
        
            foreach ($module->getPdfs() as $pdf) {
                $duration = $pdf->getEstimatedDuration() ?? 0;
                $totalMinutes += $duration;
        
                if ($moduleViewed) {
                    $userMinutes += $duration;
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

        // Si l'utilisateur est connecté, vérifier l'inscription et l'avis
        if ($user) {
            // Vérifier si l'utilisateur est inscrit à cette formation
            $userFormation = $userFormationRepository->findOneBy(['user' => $user, 'formation' => $formation]);
            $isEnrolled = $userFormation !== null;
    
            // Vérifier si l'utilisateur a déjà laissé un avis pour cette formation
            $existingReview = $reviewRepository->findOneBy([
                'user' => $user,
                'formation' => $formation
            ]);
            $hasReviewed = $existingReview !== null; // Mettre à jour la variable
        }
        $review = new Review();
        $form = $this->createForm(ReviewType::class, $review);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            // Associer l'avis à la formation
            $review->setFormation($formation);
    
            // Associer l'avis à l'utilisateur
            $user = $this->getUser();
            if ($user !== null) {
                $review->setUser($user);
            }
    
            $entityManager->persist($review);
            $entityManager->flush();
    
            // Envoi de l'email (facultatif)
            $mail->sendEmail(
                'contact@cameleon-solutions.fr',
                'Demande de contact',
                'contact@cameleon-solutions.fr',
                'Nouveau commentaire sur le site',
                'review',
                []
            );
    
            $this->addFlash('success', 'Votre avis a bien été envoyé, il sera publié après validation !');
            return $this->redirectToRoute('app_formation_show', [
                'slug' => $formation->getSlug(),
            ]);
        }

        // Récupérer les avis validés
        $reviewValidated = $reviewRepository->findBy(['isValidated' => true]);

        // Calculer la moyenne des notes
        $moyenne = 0;
        if (count($reviewValidated) > 0) {
            $total = 0;
            foreach ($reviewValidated as $avis) {
                $total += $avis->getRating();
            }
            $moyenne = $total / count($reviewValidated);
        }

        $recentReviews = $reviewRepository->findBy(
            ['isValidated' => true], // Filtre pour les avis validés
            ['createdAt' => 'DESC'],  // Trier par date de création (ou ID)
            3                        // Limiter à 6 avis
        );

        return $this->render('formation/show.html.twig', [
            'formation' => $formation,
            'inscription' => $inscription,
            'inscriptionValide' => $inscriptionValide,
            'progress' => $progress,
            'modules' => $modules,
            'totalDuration' => $totalMinutes,
            'remainingDuration' => $remainingMinutes,
            'form' => $form->createView(),
            'moyenne' => $moyenne,
            'avis' => $reviewValidated,
            'recentReviews' => $recentReviews, 
            'isEnrolled' => $isEnrolled, // Utilisé pour afficher le formulaire d'avis
            'hasReviewed' => $existingReview,
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
                return $this->redirectToRoute('app_formation_show', ['slug' => $slug]);
            }
        }
    
        $inscription = new UserFormation();
        $inscription->setUser($user);
        $inscription->setFormation($formation);

        $inscription->setIsCompleted(false);
        $formation->addUser($user);  // Ajouter l'utilisateur à la formation

        $em->persist($inscription);
        $em->flush();
    
        $this->addFlash('success', 'Vous êtes maintenant inscrit à cette formation.');
    
        return $this->redirectToRoute('app_formation_show', ['slug' => $slug]);
    }

    #[Route('/formations/individuel', name: 'formations_individuelles')]
    public function formationsIndividuelles(CategorieRepository $categorieRepository): Response
    {
        $categories = $categorieRepository->findAll();
    
        return $this->render('formation/individuelles.html.twig', [
            'categories' => $categories,
        ]);
    }

    #[Route('/formations/entreprise', name: 'formations_entreprise')]
    public function formationsEntreprise(
        Request $request,
        EntityManagerInterface $entityManager,
        MailerInterface $mailer
    ): Response {
        $demande = new DemandeEntreprise();
        $form = $this->createForm(ContactEntrepriseType::class, $demande);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $demande->setCreatedAt(new \DateTimeImmutable());
            $entityManager->persist($demande);
            $entityManager->flush();
    
            // Envoi de l'e-mail
            $email = (new Email())
                ->from($demande->getEmail())
                ->to('tonadresse@email.com') // ← à remplacer par ton adresse pro
                ->subject('Nouvelle demande entreprise')
                ->text("Nom : " . $demande->getName() . "\n"
                     . "Entreprise : " . $demande->getEntreprise() . "\n"
                     . "Email : " . $demande->getEmail() . "\n"
                     . "Message : \n" . $demande->getMessage());
    
            $mailer->send($email);
    
            $this->addFlash('success', 'Votre demande a bien été envoyée.');
            return $this->redirectToRoute('formations_entreprise');
        }
    
        return $this->render('formation/entreprise.html.twig', [
            'contactForm' => $form->createView(),
        ]);
    }

    #[Route('/formations/categorie/{slug}', name: 'formations_par_categorie')]
    public function formationsParCategorie(
        CategorieRepository $categorieRepository,
        string $slug
    ): Response {
        $categorie = $categorieRepository->findOneBy(['slug' => $slug]);

        if (!$categorie) {
            throw $this->createNotFoundException('Catégorie non trouvée');
        }

        $formations = $categorie->getFormation(); // via OneToMany dans l'entité

        return $this->render('formation/categorie.html.twig', [
            'categorie' => $categorie,
            'formations' => $formations,
        ]);
    }


}

