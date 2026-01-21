<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Form\InscriptionType;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class InscriptionController extends AbstractController
{
    #[Route('/inscription', name: 'app_inscription')]
    public function inscrire(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher,
        MailerInterface $mailer
    ): Response {
        $session = $request->getSession();
        $referer = $request->headers->get('referer');
        if ($referer && !str_contains($referer, '/inscription')) {
            $session->set('apres_inscription_redirect', $referer);
        }

        if (!$session->has('captcha_reponse')) {
            $a = random_int(1, 9);
            $b = random_int(1, 9);
            $session->set('captcha_question', sprintf('%d + %d = ?', $a, $b));
            $session->set('captcha_reponse', (string) ($a + $b));
        }

        $utilisateur = new Utilisateur();
        $form = $this->createForm(InscriptionType::class, $utilisateur);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $captcha = (string) $form->get('captcha')->getData();
            if ($captcha !== (string) $session->get('captcha_reponse')) {
                $form->addError(new FormError('Captcha incorrect.'));
            }
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $utilisateur->setPassword(
                $passwordHasher->hashPassword($utilisateur, $form->get('motDePasse')->getData())
            );
            $utilisateur->setVerifie(false);
            $jeton = bin2hex(random_bytes(32));
            $utilisateur->setJetonConfirmation($jeton);
            $utilisateur->setExpirationConfirmation((new \DateTimeImmutable())->modify('+1 day'));

            $entityManager->persist($utilisateur);
            $entityManager->flush();

            $verifyUrl = $this->generateUrl(
                'app_confirmer_inscription',
                ['jeton' => $jeton],
                UrlGeneratorInterface::ABSOLUTE_URL
            );

            $email = (new Email())
                ->from('no-reply@forum.local')
                ->to($utilisateur->getEmail())
                ->subject('Confirmez votre inscription')
                ->text("Bonjour {$utilisateur->getPseudo()},\n\nConfirmez votre compte: {$verifyUrl}\n\nLe lien expire sous 24h.");

            try {
                $mailer->send($email);
                $this->addFlash('success', 'Email de confirmation envoyé. Vérifie ta boîte mail.');
            } catch (\Throwable $e) {
                $this->addFlash('warning', 'Envoi email impossible. Lien de confirmation: '.$verifyUrl);
            }

            $session->remove('captcha_reponse');
            $session->remove('captcha_question');

            $redirect = $session->get('apres_inscription_redirect', $this->generateUrl('app_accueil'));
            return $this->redirect($redirect);
        }

        return $this->render('securite/inscription.html.twig', [
            'formulaireInscription' => $form->createView(),
            'captcha_question' => $session->get('captcha_question'),
        ]);
    }

    #[Route('/confirmer/{jeton}', name: 'app_confirmer_inscription')]
    public function confirmer(string $jeton, UtilisateurRepository $utilisateurRepository, EntityManagerInterface $entityManager): Response
    {
        $user = $utilisateurRepository->trouverParJetonConfirmation($jeton);
        if (!$user) {
            return $this->render('securite/confirmation.html.twig', [
                'status' => 'invalid',
            ]);
        }

        $expiresAt = $user->getExpirationConfirmation();
        if ($expiresAt && $expiresAt < new \DateTimeImmutable()) {
            return $this->render('securite/confirmation.html.twig', [
                'status' => 'expired',
            ]);
        }

        $user->setVerifie(true);
        $user->setJetonConfirmation(null);
        $user->setExpirationConfirmation(null);
        $entityManager->flush();

        return $this->render('securite/confirmation.html.twig', [
            'status' => 'ok',
        ]);
    }
}
