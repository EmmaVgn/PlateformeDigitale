<?php

namespace App\Entity;

use App\Repository\FormationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FormationRepository::class)]
class Formation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column]
    private ?bool $isPublished = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;

    /**
     * @var Collection<int, Module>
     */
    #[ORM\OneToMany(targetEntity: Module::class, mappedBy: 'formation')]
    private Collection $modules;

    /**
     * @var Collection<int, Quiz>
     */
    #[ORM\OneToMany(targetEntity: Quiz::class, mappedBy: 'formation')]
    private Collection $quizzes;


    #[ORM\OneToMany(mappedBy: 'formation', targetEntity: UserFormation::class)]
    private Collection $inscriptions;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $objectifDeFormation = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $programme = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $modaliteAcces = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $modaliteEvaluation = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $coutEtFinancement = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $contact = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $accessibilite = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $publicCible = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $preRequis = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $duree = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $dateFormation = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $lieu = null;

    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'formations')]
    private Collection $users;

    /**
     * @var Collection<int, UserFormation>
     */
    #[ORM\OneToMany(mappedBy: 'formation', targetEntity: UserFormation::class)]
    private Collection $userFormations;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $competences = null;


    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->title ?? 'Formation non définie';  // Retourne le titre ou un texte par défaut
    }

    public function __construct()
    {
        $this->modules = new ArrayCollection();
        $this->quizzes = new ArrayCollection();
        $this->inscriptions = new ArrayCollection();
        $this->userFormations = new ArrayCollection();
        $this->users = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function isPublished(): ?bool
    {
        return $this->isPublished;
    }

    public function setIsPublished(bool $isPublished): static
    {
        $this->isPublished = $isPublished;

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

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return Collection<int, Module>
     */
    public function getModules(): Collection
    {
        return $this->modules;
    }

    public function addModule(Module $module): static
    {
        if (!$this->modules->contains($module)) {
            $this->modules->add($module);
            $module->setFormation($this);
        }

        return $this;
    }

    public function removeModule(Module $module): static
    {
        if ($this->modules->removeElement($module)) {
            // set the owning side to null (unless already changed)
            if ($module->getFormation() === $this) {
                $module->setFormation(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Quiz>
     */
    public function getQuizzes(): Collection
    {
        return $this->quizzes;
    }

    public function addQuiz(Quiz $quiz): static
    {
        if (!$this->quizzes->contains($quiz)) {
            $this->quizzes->add($quiz);
            $quiz->setFormation($this);
        }

        return $this;
    }

    public function removeQuiz(Quiz $quiz): static
    {
        if ($this->quizzes->removeElement($quiz)) {
            // set the owning side to null (unless already changed)
            if ($quiz->getFormation() === $this) {
                $quiz->setFormation(null);
            }
        }

        return $this;
    }



    /**
     * @return Collection<int, UserFormation>
     */
    public function getInscriptions(): Collection
    {
        return $this->inscriptions;
    }

    public function addInscription(UserFormation $inscription): static
    {
        if (!$this->inscriptions->contains($inscription)) {
            $this->inscriptions->add($inscription);
            $inscription->setFormation($this);
        }

        return $this;
    }

    public function removeInscription(UserFormation $inscription): static
    {
        if ($this->inscriptions->removeElement($inscription)) {
            // set the owning side to null (unless already changed)
            if ($inscription->getFormation() === $this) {
                $inscription->setFormation(null);
            }
        }

        return $this;
    }

    public function getObjectifDeFormation(): ?string
    {
        return $this->objectifDeFormation;
    }

    public function setObjectifDeFormation(string $objectifDeFormation): static
    {
        $this->objectifDeFormation = $objectifDeFormation;

        return $this;
    }

    public function getProgramme(): ?string
    {
        return $this->programme;
    }

    public function setProgramme(string $programme): static
    {
        $this->programme = $programme;

        return $this;
    }

    public function getModaliteAcces(): ?string
    {
        return $this->modaliteAcces;
    }

    public function setModaliteAcces(string $modaliteAcces): static
    {
        $this->modaliteAcces = $modaliteAcces;

        return $this;
    }

    public function getModaliteEvaluation(): ?string
    {
        return $this->modaliteEvaluation;
    }

    public function setModaliteEvaluation(string $modaliteEvaluation): static
    {
        $this->modaliteEvaluation = $modaliteEvaluation;

        return $this;
    }

    public function getCoutEtFinancement(): ?string
    {
        return $this->coutEtFinancement;
    }

    public function setCoutEtFinancement(string $coutEtFinancement): static
    {
        $this->coutEtFinancement = $coutEtFinancement;

        return $this;
    }

    public function getContact(): ?string
    {
        return $this->contact;
    }

    public function setContact(string $contact): static
    {
        $this->contact = $contact;

        return $this;
    }

    public function getAccessibilite(): ?string
    {
        return $this->accessibilite;
    }

    public function setAccessibilite(string $accessibilite): static
    {
        $this->accessibilite = $accessibilite;

        return $this;
    }

    public function getPublicCible(): ?string
    {
        return $this->publicCible;
    }

    public function setPublicCible(string $publicCible): static
    {
        $this->publicCible = $publicCible;

        return $this;
    }

    public function getPreRequis(): ?string
    {
        return $this->preRequis;
    }

    public function setPreRequis(string $preRequis): static
    {
        $this->preRequis = $preRequis;

        return $this;
    }

    public function getDuree(): ?string
    {
        return $this->duree;
    }

    public function setDuree(string $duree): static
    {
        $this->duree = $duree;

        return $this;
    }

    public function getDateFormation(): ?string
    {
        return $this->dateFormation;
    }

    public function setDateFormation(string $dateFormation): static
    {
        $this->dateFormation = $dateFormation;

        return $this;
    }

    public function getLieu(): ?string
    {
        return $this->lieu;
    }

    public function setLieu(string $lieu): static
    {
        $this->lieu = $lieu;

        return $this;
    }

    public function getUserFormations(): Collection
    {
        return $this->userFormations;
    }

    public function addUserFormation(UserFormation $userFormation): self
    {
        if (!$this->userFormations->contains($userFormation)) {
            $this->userFormations[] = $userFormation;
            $userFormation->setFormation($this);
        }

        return $this;
    }

    public function removeUserFormation(UserFormation $userFormation): self
    {
        if ($this->userFormations->removeElement($userFormation)) {
            // set the owning side to null (unless already changed)
            if ($userFormation->getFormation() === $this) {
                $userFormation->setFormation(null);
            }
        }

        return $this;
    }

    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->addFormation($this); // Associer aussi la formation à l'utilisateur
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->removeElement($user)) {
            $user->removeFormation($this); // Retirer l'association dans l'utilisateur
        }

        return $this;
    }

    public function getCompetences(): ?string
    {
        return $this->competences;
    }

    public function setCompetences(string $competences): static
    {
        $this->competences = $competences;

        return $this;
    }
}
