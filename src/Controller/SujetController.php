<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class SujetController extends AbstractController
{
    #[Route('/sujet', name: 'app_sujet')]
    public function index(): Response
    {
        return $this->render('sujet/index.html.twig', [
            'controller_name' => 'SujetController',
        ]);
    }
}
