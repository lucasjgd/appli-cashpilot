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

    #[ORM\OneToOne(mappedBy: "depense", cascade: ["persist", "remove"])]
    private ?Recurrence $recurrence = null;
}
