<?php

namespace App\Entity;

use App\Repository\AvoirRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AvoirRepository::class)]
class Avoir
{
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Livret::class, inversedBy: "avoirs")]
    #[ORM\JoinColumn(name: "id_livret", referencedColumnName: "id", nullable: false, onDelete: "CASCADE")]
    private ?Livret $livret = null;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Categorie::class, inversedBy: "avoirs")]
    #[ORM\JoinColumn(name: "id_categ", referencedColumnName: "id", nullable: false, onDelete: "CASCADE")]
    private ?Categorie $categorie = null;

    #[ORM\Column(name: "budget_max_categ", type: "float")]
    private float $budgetMaxCateg;

    #[ORM\Column(type: "boolean", options: ["default" => true])]
    private bool $actif = true;

    public function getLivret(): ?Livret
    {
        return $this->livret;
    }

    public function setLivret(?Livret $livret): self
    {
        $this->livret = $livret;
        return $this;
    }

    public function getCategorie(): ?Categorie
    {
        return $this->categorie;
    }

    public function setCategorie(?Categorie $categorie): self
    {
        $this->categorie = $categorie;
        return $this;
    }

    public function getBudgetMaxCateg(): float
    {
        return $this->budgetMaxCateg;
    }

    public function setBudgetMaxCateg(float $budgetMaxCateg): self
    {
        $this->budgetMaxCateg = $budgetMaxCateg;
        return $this;
    }

    public function estActif(): bool
    {
        return $this->actif;
    }

    public function setActif(bool $actif): self
    {
        $this->actif = $actif;
        return $this;
    }
}
