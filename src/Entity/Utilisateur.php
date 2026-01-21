<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\UtilisateurRepository;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UtilisateurRepository::class)]
class Utilisateur implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Assert\Email]
    #[Assert\NotBlank]
    private ?string $email = null;

    #[ORM\Column]
    #[Assert\NotBlank]
    private ?string $password = null;

    #[ORM\Column(length: 50, unique: true)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 3, max: 50)]
    private ?string $pseudo = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $nom = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $prenom = null;

    #[ORM\Column(nullable: true)]
    private ?int $age = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $telephone = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $ville = null;

    #[ORM\Column]
    private bool $isModerator = false;

    #[ORM\Column(options: ['default' => false])]
    private bool $isVerified = false;

    #[ORM\Column(length: 64, nullable: true, unique: true)]
    private ?string $verificationToken = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $verificationExpiresAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $lastSeenAt = null;

    public function getId(): ?int { return $this->id; }
    public function getEmail(): ?string { return $this->email; }
    public function setEmail(string $email): self { $this->email = $email; return $this; }

    public function getPassword(): string { return $this->password; }
    public function setPassword(string $password): self { $this->password = $password; return $this; }

    public function getPseudo(): ?string { return $this->pseudo; }
    public function setPseudo(string $pseudo): self { $this->pseudo = $pseudo; return $this; }

    public function getNom(): ?string { return $this->nom; }
    public function setNom(?string $nom): self { $this->nom = $nom; return $this; }

    public function getPrenom(): ?string { return $this->prenom; }
    public function setPrenom(?string $prenom): self { $this->prenom = $prenom; return $this; }

    public function getAge(): ?int { return $this->age; }
    public function setAge(?int $age): self { $this->age = $age; return $this; }

    public function getTelephone(): ?string { return $this->telephone; }
    public function setTelephone(?string $telephone): self { $this->telephone = $telephone; return $this; }

    public function getVille(): ?string { return $this->ville; }
    public function setVille(?string $ville): self { $this->ville = $ville; return $this; }

    public function isModerator(): bool { return $this->isModerator; }
    public function setIsModerator(bool $isModerator): self { $this->isModerator = $isModerator; return $this; }

    public function isVerified(): bool { return $this->isVerified; }
    public function setIsVerified(bool $isVerified): self { $this->isVerified = $isVerified; return $this; }

    public function getVerificationToken(): ?string { return $this->verificationToken; }
    public function setVerificationToken(?string $verificationToken): self { $this->verificationToken = $verificationToken; return $this; }

    public function getVerificationExpiresAt(): ?\DateTimeImmutable { return $this->verificationExpiresAt; }
    public function setVerificationExpiresAt(?\DateTimeImmutable $verificationExpiresAt): self { $this->verificationExpiresAt = $verificationExpiresAt; return $this; }

    public function getLastSeenAt(): ?\DateTimeImmutable { return $this->lastSeenAt; }
    public function setLastSeenAt(?\DateTimeImmutable $lastSeenAt): self { $this->lastSeenAt = $lastSeenAt; return $this; }

    public function getUserIdentifier(): string { return $this->email; }
    public function getRoles(): array { return $this->isModerator ? ['ROLE_USER','ROLE_MODERATOR'] : ['ROLE_USER']; }
    public function eraseCredentials() {}
}
