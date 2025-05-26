<?php

namespace App\Entity;

use App\Repository\UtilisateurRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;


#[ORM\Entity(repositoryClass: UtilisateurRepository::class)]
class Utilisateur implements PasswordAuthenticatedUserInterface
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

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;
        return $this;
    }

    public function getMail(): ?string
    {
        return $this->email;
    }

    public function setMail(string $mail): static
    {
        $this->email = $mail;
        return $this;
    }
    public function getMdp(): ?string
    {
        return $this->mdp;
    }

    public function getPassword(): ?string
    {
        return $this->mdp;
    }

    public function setMdp(string $mdp): static
    {
        $this->mdp = $mdp;
        return $this;
    }

    public function getLivrets(): Collection
    {
        return $this->livrets;
    }
}

