<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\ThemeRepository;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    #[Route('/home', name: 'app_home_legacy')]
    public function index(Request $request, ThemeRepository $themeRepository): Response
    {
        $page = max(1, (int) $request->query->get('page', 1));
        $limit = 5;
        $themes = $themeRepository->findPageWithStats($page, $limit);
        $total = $themeRepository->countAll();
        $pages = (int) ceil($total / $limit);

        return $this->render('home/index.html.twig', [
            'themes' => $themes,
            'page' => $page,
            'pages' => $pages,
        ]);
    }
}
