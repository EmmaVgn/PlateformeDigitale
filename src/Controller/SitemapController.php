<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class SitemapController extends AbstractController{
    #[Route('/sitemap.xml', name: 'sitemap', defaults: ['_format' => 'xml'])]
    public function index(): Response
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'application/xml');

        $xml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<urlset 
    xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url>
        <loc>{{ url('homepage') }}</loc>
    </url>
    <url>
        <loc>{{ url('app_formations') }}</loc>
    </url>
    <url>
        <loc>{{ url('app_register') }}</loc>
    </url>
    <url>
        <loc>{{ url('legal_mentions') }}</loc>
    </url>
    <url>
        <loc>{{ url('legal_cgu') }}</loc>
    </url>
    <url>
        <loc>{{ url('legal_sitemap') }}</loc>
    </url>
    <url>
    <loc>https://www.cameleon-solutions.fr</loc>
</url>
<url>
    <loc>https://learning.cameleon-solutions.fr</loc>
</url>

</urlset>
XML;

        $response->setContent($xml);
        return $response;
    }
}
