<?php

namespace App\Security;

use App\Entity\Utilisateur;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class VerifUtilisateurChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user): void
    {
        if (!$user instanceof Utilisateur) {
            return;
        }

        if (!$user->isVerifie()) {
            throw new CustomUserMessageAuthenticationException(
                'Veuillez confirmer votre inscription par email avant de vous connecter.'
            );
        }
    }

    public function checkPostAuth(UserInterface $user): void
    {
    }
}
