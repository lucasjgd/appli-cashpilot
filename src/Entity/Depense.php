<?php

namespace App\Entity;

use App\Repository\DepenseRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DepenseRepository::class)]
class Depense
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private float $montantDepense;

    #[ORM\Column(length: 255)]
    private string $descriptionDepense;

    #[ORM\Column(type: "date")]
    private \DateTimeInterface $dateDepense;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Livret $livret = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Categorie $categorie = null;

    #[ORM\Column(type: "boolean")]
    private bool $estRecurrente = false;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMontant(): float
    {
        return $this->montantDepense;
    }

    public function setMontantDepense(float $montantDepense): self
    {
        $this->montantDepense = $montantDepense;
        return $this;
    }

    public function getDescription(): string
    {
        return $this->descriptionDepense;
    }

    public function setDescriptionDepense(string $descriptionDepense): self
    {
        $this->descriptionDepense = $descriptionDepense;
        return $this;
    }

    public function getDate(): \DateTimeInterface
    {
        return $this->dateDepense;
    }

    public function setDateDepense(\DateTimeInterface $dateDepense): self
    {
        $this->dateDepense = $dateDepense;
        return $this;
    }

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

    public function estRecurrente(): bool
    {
        return $this->estRecurrente;
    }

    public function setEstRecurrente(bool $estRecurrente): self
    {
        $this->estRecurrente = $estRecurrente;
        return $this;
    }
}
