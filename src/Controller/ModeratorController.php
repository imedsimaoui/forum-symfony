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

final class ModeratorController extends AbstractController
{
    #[Route('/moderator', name: 'app_moderator')]
    public function index(Request $request, EntityManagerInterface $entityManager, UtilisateurRepository $utilisateurRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_MODERATOR');

        $theme = new Theme();
        $form = $this->createForm(ThemeType::class, $theme);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($theme);
            $entityManager->flush();
            $this->addFlash('success', 'Thème ajouté.');
            return $this->redirectToRoute('app_moderator');
        }

        $users = $utilisateurRepository->findBy([], ['id' => 'DESC']);

        return $this->render('moderator/index.html.twig', [
            'themeForm' => $form->createView(),
            'users' => $users,
        ]);
    }

    #[Route('/moderator/message/{id}/edit', name: 'app_moderator_message_edit')]
    public function editMessage(Message $message, Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_MODERATOR');

        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Message modifié.');
            return $this->redirectToRoute('app_sujet', ['id' => $message->getSujet()->getId()]);
        }

        return $this->render('moderator/edit_message.html.twig', [
            'messageForm' => $form->createView(),
            'message' => $message,
        ]);
    }

    #[Route('/moderator/message/{id}/delete', name: 'app_moderator_message_delete', methods: ['POST'])]
    public function deleteMessage(Message $message, Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_MODERATOR');

        if ($this->isCsrfTokenValid('delete_message_'.$message->getId(), $request->request->get('_token'))) {
            $entityManager->remove($message);
            $entityManager->flush();
            $this->addFlash('success', 'Message supprimé.');
        }

        return $this->redirectToRoute('app_sujet', ['id' => $message->getSujet()->getId()]);
    }
}
