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
}
