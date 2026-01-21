<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class Sujet
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private string $titre;

    #[ORM\ManyToOne(inversedBy: 'sujets')]
    #[ORM\JoinColumn(nullable: false)]
    private Theme $theme;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private Utilisateur $auteur;

    #[ORM\OneToMany(mappedBy: 'sujet', targetEntity: Message::class)]
    private Collection $messages;

    #[ORM\Column]
    private ?\DateTimeImmutable $creeLe = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $modifieLe = null;

    public function __construct()
    {
        $this->messages = new ArrayCollection();
    }

    #[ORM\PrePersist]
    public function setCreeLeValue(): void
    {
        $this->creeLe = new \DateTimeImmutable();
    }

    #[ORM\PreUpdate]
    public function setModifieLeValue(): void
    {
        $this->modifieLe = new \DateTimeImmutable();
    }

    public function getId(): ?int { return $this->id; }
    public function getTitre(): string { return $this->titre; }
    public function setTitre(string $titre): self { $this->titre = $titre; return $this; }

    public function getTheme(): Theme { return $this->theme; }
    public function setTheme(Theme $theme): self { $this->theme = $theme; return $this; }

    public function getAuteur(): Utilisateur { return $this->auteur; }
    public function setAuteur(Utilisateur $auteur): self { $this->auteur = $auteur; return $this; }

    public function getMessages(): Collection { return $this->messages; }

    public function getCreeLe(): ?\DateTimeImmutable { return $this->creeLe; }
    public function getModifieLe(): ?\DateTimeImmutable { return $this->modifieLe; }
}
