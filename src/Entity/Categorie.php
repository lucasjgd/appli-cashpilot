<?php

namespace App\Entity;

use App\Repository\CategorieRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: CategorieRepository::class)]
class Categorie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private string $libelle;

    #[ORM\OneToMany(mappedBy: "categorie", targetEntity: Avoir::class)]
    private Collection $avoirs;

    #[ORM\OneToMany(mappedBy: "categorie", targetEntity: Depense::class)]
    private Collection $depenses;

     public function getId(): ?int
    {
        return $this->id;   
    }

    public function getLibelle(): string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): self
    {
        $this->libelle = $libelle;
        return $this;
    }

    public function getDepenses()
{
    return $this->depenses;
}
}
