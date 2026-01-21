<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\UtilisateurRepository;

final class ApiController extends AbstractController
{
    #[Route('/api', name: 'app_api')]
    public function index(): Response
    {
        return $this->render('api/index.html.twig', [
            'controller_name' => 'ApiController',
        ]);
    }

    #[Route('/api/online-count', name: 'app_api_online_count')]
    public function onlineCount(UtilisateurRepository $utilisateurRepository): JsonResponse
    {
        $since = (new \DateTimeImmutable())->modify('-15 seconds');
        $count = $utilisateurRepository->countOnlineSince($since);

        return new JsonResponse(['count' => $count]);
    }
}
