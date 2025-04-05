<?php

// src/Controller/ModuleController.php
namespace App\Controller;

use App\Entity\Module;
use App\Form\ModuleFormType;
use App\Repository\ModuleRepository;
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

}
