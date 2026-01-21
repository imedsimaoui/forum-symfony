<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity]
class Theme
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    private string $titre;

    #[ORM\OneToMany(mappedBy: 'theme', targetEntity: Sujet::class)]
    private Collection $sujets;

    public function __construct()
    {
        $this->sujets = new ArrayCollection();
    }
}
