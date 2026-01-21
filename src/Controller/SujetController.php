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
    public function show(
        Sujet $sujet,
        MessageRepository $messageRepository,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response
    {
        $messages = $messageRepository->findBySujetOrdered($sujet);

        $newMessage = new Message();
        $form = $this->createForm(MessageType::class, $newMessage, [
            'action' => $this->generateUrl('app_sujet', ['id' => $sujet->getId()]),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->denyAccessUnlessGranted('ROLE_USER');

            $newMessage->setSujet($sujet);
            $newMessage->setAuteur($this->getUser());
            $entityManager->persist($newMessage);
            $entityManager->flush();

            return $this->redirectToRoute('app_sujet', ['id' => $sujet->getId()]);
        }

        return $this->render('sujet/index.html.twig', [
            'sujet' => $sujet,
            'messages' => $messages,
            'messageForm' => $form->createView(),
        ]);
    }
}
