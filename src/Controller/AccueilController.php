<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\ThemeRepository;

final class AccueilController extends AbstractController
{
    #[Route('/', name: 'app_accueil')]
    #[Route('/home', name: 'app_accueil_legacy')]
    public function accueil(Request $request, ThemeRepository $repoTheme): Response
    {
        $page = max(1, (int) $request->query->get('page', 1));
        $limite = 5;
        $themes = $repoTheme->trouverPageAvecStats($page, $limite);
        $total = $repoTheme->compterTous();
        $pages = (int) ceil($total / $limite);

        return $this->render('accueil/index.html.twig', [
            'themes' => $themes,
            'page' => $page,
            'pages' => $pages,
        ]);
    }
}
