<?php

// src/Controller/ModuleController.php
namespace App\Controller;

use App\Entity\Module;
use App\Entity\ModuleView;
use App\Form\ModuleFormType;
use App\Repository\ModuleRepository;
use App\Repository\ModuleViewRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class ModuleController extends AbstractController
{
    #[Route('/module/new', name: 'app_module_new')]
    public function new(Request $request, ModuleRepository $moduleRepository): Response
    {
        $module = new Module();
        $form = $this->createForm(ModuleFormType::class, $module);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // 🔁 Important : rattacher chaque PDF à son module
            foreach ($module->getPdfs() as $pdf) {
                $pdf->setModules($module); // relation inverse
            }

            // VichUploaderBundle gère tout le reste : upload du fileObj, du imageFile, etc.
            $moduleRepository->save($module, true);

            $this->addFlash('success', 'Module créé avec succès !');
            return $this->redirectToRoute('app_module_index');
        }

        return $this->render('module/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/module/{id}', name: 'app_module_show')]
    public function show(
        Module $module,
        ModuleViewRepository $moduleViewRepository,
        EntityManagerInterface $entityManager
    ): Response {
        $user = $this->getUser();

        // Vérifie si ce module a déjà été vu par cet utilisateur
        $existingView = $moduleViewRepository->findOneBy([
            'user' => $user,
            'module' => $module,
        ]);

        // Si non, on enregistre la vue
        if (!$existingView) {
            $view = new \App\Entity\ModuleView();
            $view->setUser($user);
            $view->setModule($module);
            $entityManager->persist($view);
            $entityManager->flush();
        }

        return $this->render('module/show.html.twig', [
            'module' => $module,
        ]);
    }

    #[Route('/module/view/{id}', name: 'module_view')]
public function view(Module $module, Request $request, EntityManagerInterface $em): Response
{
    $user = $this->getUser();

    // Vérifie s'il y a déjà un ModuleView
    $existingView = $em->getRepository(ModuleView::class)->findOneBy([
        'user' => $user,
        'module' => $module,
    ]);

    if (!$existingView) {
        $view = new ModuleView();
        $view->setUser($user);
        $view->setModule($module);
        $view->setViewedAt(new \DateTimeImmutable());

        $em->persist($view);
        $em->flush();
    }

    // Met à jour la progression (tu l’as probablement déjà fait ici)

    // Redirige vers le fichier PDF
    $file = $request->query->get('file');

    return $this->redirect('/uploads/pdfs/' . $file);
}



}
