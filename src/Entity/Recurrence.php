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

    #[ORM\Column(length: 50)]
    private string $frequence;

    #[ORM\Column(type: "date")]
    private \DateTimeInterface $dateDebut;

    #[ORM\Column(type: "date", nullable: true)]
    private ?\DateTimeInterface $dateFin;

    #[ORM\OneToOne(inversedBy: "recurrence")]
    #[ORM\JoinColumn(nullable: false)]
    private ?Depense $depense = null;
}
