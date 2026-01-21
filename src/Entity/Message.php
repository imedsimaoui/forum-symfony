<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class Message
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank]
    #[Assert\Length(max: 5000)]
    private string $contenu;

    #[ORM\ManyToOne(inversedBy: 'messages')]
    #[ORM\JoinColumn(nullable: false)]
    private Sujet $sujet;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private Utilisateur $auteur;

    #[ORM\Column]
    private ?\DateTimeImmutable $creeLe = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $modifieLe = null;

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
    public function getContenu(): string { return $this->contenu; }
    public function setContenu(string $contenu): self { $this->contenu = $contenu; return $this; }

    public function getSujet(): Sujet { return $this->sujet; }
    public function setSujet(Sujet $sujet): self { $this->sujet = $sujet; return $this; }

    public function getAuteur(): Utilisateur { return $this->auteur; }
    public function setAuteur(Utilisateur $auteur): self { $this->auteur = $auteur; return $this; }

    public function getCreeLe(): ?\DateTimeImmutable { return $this->creeLe; }
    public function getModifieLe(): ?\DateTimeImmutable { return $this->modifieLe; }
}
