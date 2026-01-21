<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Form\RegistrationFormType;
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

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher,
        MailerInterface $mailer
    ): Response {
        $session = $request->getSession();
        $referer = $request->headers->get('referer');
        if ($referer && !str_contains($referer, '/register')) {
            $session->set('after_register_redirect', $referer);
        }

        if (!$session->has('captcha_answer')) {
            $a = random_int(1, 9);
            $b = random_int(1, 9);
            $session->set('captcha_question', sprintf('%d + %d = ?', $a, $b));
            $session->set('captcha_answer', (string) ($a + $b));
        }

        $user = new Utilisateur();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $captcha = (string) $form->get('captcha')->getData();
            if ($captcha !== (string) $session->get('captcha_answer')) {
                $form->addError(new FormError('Captcha incorrect.'));
            }
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $passwordHasher->hashPassword($user, $form->get('plainPassword')->getData())
            );
            $user->setIsVerified(false);
            $token = bin2hex(random_bytes(32));
            $user->setVerificationToken($token);
            $user->setVerificationExpiresAt((new \DateTimeImmutable())->modify('+1 day'));

            $entityManager->persist($user);
            $entityManager->flush();

            $verifyUrl = $this->generateUrl(
                'app_verify_email',
                ['token' => $token],
                UrlGeneratorInterface::ABSOLUTE_URL
            );

            $email = (new Email())
                ->from('no-reply@forum.local')
                ->to($user->getEmail())
                ->subject('Confirmez votre inscription')
                ->text("Bonjour {$user->getPseudo()},\n\nConfirmez votre compte: {$verifyUrl}\n\nLe lien expire sous 24h.");

            try {
                $mailer->send($email);
                $this->addFlash('success', 'Email de confirmation envoyé. Vérifie ta boîte mail.');
            } catch (\Throwable $e) {
                $this->addFlash('warning', 'Envoi email impossible. Lien de confirmation: '.$verifyUrl);
            }

            $session->remove('captcha_answer');
            $session->remove('captcha_question');

            $redirect = $session->get('after_register_redirect', $this->generateUrl('app_home'));
            return $this->redirect($redirect);
        }

        return $this->render('security/register.html.twig', [
            'registrationForm' => $form->createView(),
            'captcha_question' => $session->get('captcha_question'),
        ]);
    }

    #[Route('/verify/{token}', name: 'app_verify_email')]
    public function verifyEmail(string $token, UtilisateurRepository $utilisateurRepository, EntityManagerInterface $entityManager): Response
    {
        $user = $utilisateurRepository->findOneByVerificationToken($token);
        if (!$user) {
            return $this->render('security/verify_result.html.twig', [
                'status' => 'invalid',
            ]);
        }

        $expiresAt = $user->getVerificationExpiresAt();
        if ($expiresAt && $expiresAt < new \DateTimeImmutable()) {
            return $this->render('security/verify_result.html.twig', [
                'status' => 'expired',
            ]);
        }

        $user->setIsVerified(true);
        $user->setVerificationToken(null);
        $user->setVerificationExpiresAt(null);
        $entityManager->flush();

        return $this->render('security/verify_result.html.twig', [
            'status' => 'ok',
        ]);
    }
}
