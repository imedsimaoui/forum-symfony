<?php

namespace App\Controller;

use App\Entity\Message;
use App\Entity\Sujet;
use App\Entity\Theme;
use App\Form\NouveauSujetType;
use App\Repository\SujetRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ThemeController extends AbstractController
{
    #[Route('/theme/{id}', name: 'app_theme', requirements: ['id' => '\d+'])]
    public function afficher(
        Theme $theme,
        Request $request,
        SujetRepository $repoSujet,
        EntityManagerInterface $entityManager
    ): Response
    {
        $page = max(1, (int) $request->query->get('page', 1));
        $limite = 10;
        $sujets = $repoSujet->trouverPageParTheme($theme, $page, $limite);
        $total = $repoSujet->compterParTheme($theme);
        $pages = (int) ceil($total / $limite);

        $nouveauSujet = new Sujet();
        $form = $this->createForm(NouveauSujetType::class, $nouveauSujet, [
            'action' => $this->generateUrl('app_theme', ['id' => $theme->getId()]),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->denyAccessUnlessGranted('ROLE_USER');

            $nouveauSujet->setTheme($theme);
            $nouveauSujet->setAuteur($this->getUser());
            $entityManager->persist($nouveauSujet);

            $message = new Message();
            $message->setSujet($nouveauSujet);
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
            'formulaireNouveauSujet' => $form->createView(),
        ]);
    }
}
