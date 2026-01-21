<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class Theme
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private string $titre;

    #[ORM\OneToMany(mappedBy: 'theme', targetEntity: Sujet::class)]
    private Collection $sujets;

    #[ORM\Column]
    private ?\DateTimeImmutable $creeLe = null;

    public function __construct()
    {
        $this->sujets = new ArrayCollection();
    }

    #[ORM\PrePersist]
    public function setCreeLeValue(): void
    {
        $this->creeLe = new \DateTimeImmutable();
    }

    public function getId(): ?int { return $this->id; }
    public function getTitre(): string { return $this->titre; }
    public function setTitre(string $titre): self { $this->titre = $titre; return $this; }
    public function getSujets(): Collection { return $this->sujets; }
    public function getCreeLe(): ?\DateTimeImmutable { return $this->creeLe; }
}
