<?php

namespace App\Controller;

use App\Entity\Message;
use App\Entity\Sujet;
use App\Entity\Theme;
use App\Form\NewSujetType;
use App\Repository\SujetRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ThemeController extends AbstractController
{
    #[Route('/theme/{id}', name: 'app_theme', requirements: ['id' => '\d+'])]
    public function show(
        Theme $theme,
        Request $request,
        SujetRepository $sujetRepository,
        EntityManagerInterface $entityManager
    ): Response
    {
        $page = max(1, (int) $request->query->get('page', 1));
        $limit = 10;
        $sujets = $sujetRepository->findPageByTheme($theme, $page, $limit);
        $total = $sujetRepository->countByTheme($theme);
        $pages = (int) ceil($total / $limit);

        $newSujet = new Sujet();
        $form = $this->createForm(NewSujetType::class, $newSujet, [
            'action' => $this->generateUrl('app_theme', ['id' => $theme->getId()]),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->denyAccessUnlessGranted('ROLE_USER');

            $newSujet->setTheme($theme);
            $newSujet->setAuteur($this->getUser());
            $entityManager->persist($newSujet);

            $message = new Message();
            $message->setSujet($newSujet);
            $message->setAuteur($this->getUser());
            $message->setContenu($form->get('contenu')->getData());
            $entityManager->persist($message);

            $entityManager->flush();

            return $this->redirectToRoute('app_theme', ['id' => $theme->getId()]);
        }

        return $this->render('theme/index.html.twig', [
            'theme' => $theme,
            'sujets' => $sujets,
            'page' => $page,
            'pages' => $pages,
            'newSujetForm' => $form->createView(),
        ]);
    }
}
