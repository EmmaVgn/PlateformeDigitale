<?php

namespace App\Entity;

use App\Entity\User; 
use App\Repository\UserFormationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserFormationRepository::class)]
class UserFormation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'userFormations')]
    private ?User $user = null;

    #[ORM\ManyToOne(targetEntity: Formation::class, inversedBy: 'userFormations')]
    private ?Formation $formation = null;

    #[ORM\Column]
    private ?int $progression = 0;

    #[ORM\Column]
    private ?bool $isCompleted = false;

    #[ORM\Column]
    private ?\DateTimeImmutable $dateInscription = null;

    #[ORM\Column(type: 'boolean')]
    private bool $isValidated = false;

    /**
     * Méthode __toString() pour afficher une représentation lisible de UserFormation
     */
    public function __toString(): string
    {
        // Vous pouvez choisir ce que vous souhaitez afficher ici, par exemple :
        return $this->user->getFirstname() . ' - ' . $this->formation->getTitle();  // Affiche le prénom de l'utilisateur et le titre de la formation
    }
    
    public function __construct()
    {
        $this->dateInscription = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $User): static
    {
        $this->user = $User;

        return $this;
    }

    public function getFormation(): ?Formation
    {
        return $this->formation;
    }

    public function setFormation(?Formation $formation): static
    {
        $this->formation = $formation;

        return $this;
    }

    public function getProgression(): ?int
    {
        return $this->progression;
    }

    public function setProgression(int $progression): static
    {
        $this->progression = $progression;

        return $this;
    }

    public function isCompleted(): ?bool
    {
        return $this->isCompleted;
    }

    public function setIsCompleted(bool $isCompleted): static
    {
        $this->isCompleted = $isCompleted;

        return $this;
    }

    public function getDateInscription(): ?\DateTimeImmutable
    {
        return $this->dateInscription;
    }

    public function setDateInscription(\DateTimeImmutable $dateInscription): static
    {
        $this->dateInscription = $dateInscription;

        return $this;
    }

    public function getisValidated(): bool
    {
        return $this->isValidated;
    }

    public function setIsValidated(bool $isValidated): static
    {
        $this->isValidated = $isValidated;
        return $this;
    }
}
