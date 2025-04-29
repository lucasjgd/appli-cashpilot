<?php

namespace App\Entity;

use App\Repository\UtilisateurRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: UtilisateurRepository::class)]
class Utilisateur
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private string $nom;

    #[ORM\Column(length: 100, unique: true)]
    private string $email;

    #[ORM\Column(length: 255)]
    private string $mdp;

    #[ORM\OneToMany(mappedBy: "utilisateur", targetEntity: Livret::class)]
    private Collection $livrets;

}

