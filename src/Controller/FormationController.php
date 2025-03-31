<?php

namespace App\Controller;

use App\Repository\FormationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\UserFormation;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;

class FormationController extends AbstractController
{
    #[Route('/formations', name: 'app_formations')]
    public function index(FormationRepository $formationRepository): Response
    {
        $formations = $formationRepository->findBy(['isPublished' => true]);

        return $this->render('formation/index.html.twig', [
            'formations' => $formations,
        ]);
    }

    #[Route('/formation/{slug}', name: 'app_formation_show')]
    public function show(string $slug, FormationRepository $formationRepository): Response
    {
        $formation = $formationRepository->findOneBy(['slug' => $slug]);

        if (!$formation || !$formation->isPublished()) {
            throw $this->createNotFoundException('Formation introuvable.');
        }

        $inscription = null;
        if ($this->getUser()) {
            foreach ($formation->getInscriptions() as $item) {
                if ($item->getUser() === $this->getUser()) {
                    $inscription = $item;
                    break;
                }
            }
        }

        return $this->render('formation/show.html.twig', [
            'formation' => $formation,
            'inscription' => $inscription,
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
        $inscription->setProgression(0);
        $inscription->setIsCompleted(false);
    
        $em->persist($inscription);
        $em->flush();
    
        $this->addFlash('success', 'Vous êtes maintenant inscrit à cette formation.');
    
        return $this->redirectToRoute('formation_show', ['slug' => $slug]);
    }


}

