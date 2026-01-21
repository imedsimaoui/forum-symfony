<?php

namespace App\Controller;

use App\Entity\Message;
use App\Entity\Sujet;
use App\Form\MessageType;
use App\Repository\MessageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class SujetController extends AbstractController
{
    #[Route('/sujet/{id}', name: 'app_sujet', requirements: ['id' => '\d+'])]
    public function afficher(
        Sujet $sujet,
        MessageRepository $repoMessage,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response
    {
        $messages = $repoMessage->trouverParSujetTrie($sujet);

        $nouveauMessage = new Message();
        $form = $this->createForm(MessageType::class, $nouveauMessage, [
            'action' => $this->generateUrl('app_sujet', ['id' => $sujet->getId()]),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->denyAccessUnlessGranted('ROLE_USER');

            $nouveauMessage->setSujet($sujet);
            $nouveauMessage->setAuteur($this->getUser());
            $entityManager->persist($nouveauMessage);
            $entityManager->flush();

            return $this->redirectToRoute('app_sujet', ['id' => $sujet->getId()]);
        }

        return $this->render('sujet/index.html.twig', [
            'sujet' => $sujet,
            'messages' => $messages,
            'formulaireMessage' => $form->createView(),
        ]);
    }
}
