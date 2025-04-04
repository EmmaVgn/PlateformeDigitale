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
            // Handling the file upload
            /** @var UploadedFile $file */
            $file = $form->get('file')->getData();

            if ($file) {
                // Generate a unique name for the file before saving it
                $fileName = uniqid().'.'.$file->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $file->move(
                        $this->getParameter('modules_directory'), // Directory for saving files
                        $fileName
                    );
                } catch (FileException $e) {
                    // Handle exception if something goes wrong during file upload
                    $this->addFlash('error', 'There was an error uploading the file.');
                    return $this->redirectToRoute('app_module_new');
                }

                // Update the 'file' property to store the file name in the database
                $module->setFile($fileName);
            }

            // Persist the module object
            $moduleRepository->save($module, true);

            // Redirect to a new route or display success message
            return $this->redirectToRoute('app_module_index');
        }

        return $this->render('module/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
