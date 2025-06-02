<?php

namespace App\Entity;

use App\Repository\RecurrenceRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RecurrenceRepository::class)]
class Recurrence
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'integer')]
    private int $frequence;

    #[ORM\Column(type: "date")]
    private \DateTimeInterface $dateDebut;

    #[ORM\Column(type: "date", nullable: true)]
    private ?\DateTimeInterface $dateFin;

    #[ORM\OneToOne(inversedBy: "recurrence")]
    #[ORM\JoinColumn(nullable: false)]
    private ?Depense $depense = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFrequence(): string
    {
        return $this->frequence;
    }

    public function setFrequence(int $frequence): self
    {
        $this->frequence = $frequence;
        return $this;
    }

    public function getDateDebut(): \DateTimeInterface
    {
        return $this->dateDebut;
    }

    public function setDateDebut(\DateTimeInterface $dateDebut): self
    {
        $this->dateDebut = $dateDebut;
        return $this;
    }

    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->dateFin;
    }

    public function setDateFin(?\DateTimeInterface $dateFin): self
    {
        $this->dateFin = $dateFin;
        return $this;
    }

    public function getDepense(): ?Depense
    {
        return $this->depense;
    }

    public function setDepense(Depense $depense): self
    {
        $this->depense = $depense;
        return $this;
    }
}
