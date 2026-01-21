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
    public function pageApi(): Response
    {
        return $this->render('api/index.html.twig');
    }

    #[Route('/api/compteur-connectes', name: 'app_api_compteur_connectes')]
    public function compteurConnectes(UtilisateurRepository $utilisateurRepository): JsonResponse
    {
        $depuis = (new \DateTimeImmutable())->modify('-15 seconds');
        $compteur = $utilisateurRepository->compterConnectesDepuis($depuis);

        return new JsonResponse(['count' => $compteur]);
    }
}
