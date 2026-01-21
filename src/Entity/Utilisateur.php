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
    private bool $moderateur = false;

    #[ORM\Column(options: ['default' => false])]
    private bool $verifie = false;

    #[ORM\Column(length: 64, nullable: true, unique: true)]
    private ?string $jetonConfirmation = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $expirationConfirmation = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $derniereActiviteAt = null;

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

    public function isModerateur(): bool { return $this->moderateur; }
    public function setModerateur(bool $moderateur): self { $this->moderateur = $moderateur; return $this; }

    public function isVerifie(): bool { return $this->verifie; }
    public function setVerifie(bool $verifie): self { $this->verifie = $verifie; return $this; }

    public function getJetonConfirmation(): ?string { return $this->jetonConfirmation; }
    public function setJetonConfirmation(?string $jetonConfirmation): self { $this->jetonConfirmation = $jetonConfirmation; return $this; }

    public function getExpirationConfirmation(): ?\DateTimeImmutable { return $this->expirationConfirmation; }
    public function setExpirationConfirmation(?\DateTimeImmutable $expirationConfirmation): self { $this->expirationConfirmation = $expirationConfirmation; return $this; }

    public function getDerniereActiviteAt(): ?\DateTimeImmutable { return $this->derniereActiviteAt; }
    public function setDerniereActiviteAt(?\DateTimeImmutable $derniereActiviteAt): self { $this->derniereActiviteAt = $derniereActiviteAt; return $this; }

    public function getUserIdentifier(): string { return $this->email; }
    public function getRoles(): array { return $this->moderateur ? ['ROLE_USER','ROLE_MODERATOR'] : ['ROLE_USER']; }
    public function eraseCredentials() {}
}
