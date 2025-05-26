<?php

namespace App\Entity;

use App\Repository\LivretRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: LivretRepository::class)]
class Livret
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private string $nomLivret;

    #[ORM\ManyToOne(inversedBy: "livrets")]
    #[ORM\JoinColumn(nullable: false)]
    private ?Utilisateur $utilisateur = null;

    #[ORM\OneToMany(mappedBy: "livret", targetEntity: Avoir::class)]
    private Collection $avoirs;

    #[ORM\OneToMany(mappedBy: "livret", targetEntity: Depense::class)]
    private Collection $depenses;

    public function getId(): ?int
    {
        return $this->id;
    }
    public function getNomLivret(): ?string
    {
        return $this->nomLivret;
    }

    public function setNom(string $nomLivret): static
    {
        $this->nomLivret = $nomLivret;
        return $this;
    }

    public function setUtilisateur(?Utilisateur $utilisateur): static
    {
        $this->utilisateur = $utilisateur;
        return $this;
    }

    public function getAvoirs()
    {
        return $this->avoirs;
    }
}
