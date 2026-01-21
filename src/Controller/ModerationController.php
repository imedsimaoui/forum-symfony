<?php

namespace App\Controller;

use App\Entity\Message;
use App\Entity\Theme;
use App\Form\MessageType;
use App\Form\ThemeType;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ModerationController extends AbstractController
{
    #[Route('/moderation', name: 'app_moderation')]
    public function tableau(Request $request, EntityManagerInterface $entityManager, UtilisateurRepository $repoUtilisateur): Response
    {
        $this->denyAccessUnlessGranted('ROLE_MODERATOR');

        $theme = new Theme();
        $form = $this->createForm(ThemeType::class, $theme);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($theme);
            $entityManager->flush();
            $this->addFlash('success', 'Thème ajouté.');
            return $this->redirectToRoute('app_moderation');
        }

        $utilisateurs = $repoUtilisateur->findBy([], ['id' => 'DESC']);

        return $this->render('moderation/index.html.twig', [
            'formulaireTheme' => $form->createView(),
            'utilisateurs' => $utilisateurs,
        ]);
    }

    #[Route('/moderation/message/{id}/modifier', name: 'app_moderation_message_modifier')]
    public function modifierMessage(Message $message, Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_MODERATOR');

        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Message modifié.');
            return $this->redirectToRoute('app_sujet', ['id' => $message->getSujet()->getId()]);
        }

        return $this->render('moderation/modifier_message.html.twig', [
            'formulaireMessage' => $form->createView(),
            'message' => $message,
        ]);
    }

    #[Route('/moderation/message/{id}/supprimer', name: 'app_moderation_message_supprimer', methods: ['POST'])]
    public function supprimerMessage(Message $message, Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_MODERATOR');

        if ($this->isCsrfTokenValid('supprimer_message_'.$message->getId(), $request->request->get('_token'))) {
            $entityManager->remove($message);
            $entityManager->flush();
            $this->addFlash('success', 'Message supprimé.');
        }

        return $this->redirectToRoute('app_sujet', ['id' => $message->getSujet()->getId()]);
    }
}
