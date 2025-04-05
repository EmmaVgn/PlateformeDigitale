<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class LegalController extends AbstractController{
    #[Route('/mentions-legales', name: 'legal_mentions')]
    public function index(): Response
    {
        return $this->render('legal/index.html.twig');
    }

    #[Route('/conditions-generales-utilisation', name: 'legal_cgu')]
    public function cgu(): Response
    {
        return $this->render('legal/cgu.html.twig');
    }

    #[Route('/plan-du-site', name: 'legal_sitemap')]
    public function sitemap(): Response
    {
        return $this->render('legal/sitemap.html.twig');
    }


}
