<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FrontendController
{
    #[Route('/', name: 'frontend_home')]
    public function home(): Response
    {
       $html = file_get_contents(__DIR__ . '/../../public/frontend/index.html');
        return new Response($html);
    }
}
