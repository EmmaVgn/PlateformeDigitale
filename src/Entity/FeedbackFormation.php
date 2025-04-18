<?php

namespace App\Entity;

use App\Repository\FeedbackFormationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FeedbackFormationRepository::class)]
class FeedbackFormation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'feedbackFormations')]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'feedbackFormations')]
    private ?Formation $formation = null;

    #[ORM\Column]
    private ?int $noteContenu = null;

    #[ORM\Column]
    private ?int $noteSupport = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $commentaireLibre = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;
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

    public function getNoteContenu(): ?int
    {
        return $this->noteContenu;
    }

    public function setNoteContenu(int $noteContenu): static
    {
        $this->noteContenu = $noteContenu;
        return $this;
    }

    public function getNoteSupport(): ?int
    {
        return $this->noteSupport;
    }

    public function setNoteSupport(int $noteSupport): static
    {
        $this->noteSupport = $noteSupport;
        return $this;
    }

    public function getCommentaireLibre(): ?string
    {
        return $this->commentaireLibre;
    }

    public function setCommentaireLibre(string $commentaireLibre): static
    {
        $this->commentaireLibre = $commentaireLibre;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }
}