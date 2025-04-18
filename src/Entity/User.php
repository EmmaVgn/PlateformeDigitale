<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;
    private ?string $plainPassword = null;

    #[ORM\Column]
    private bool $isVerified = false;

    #[ORM\Column(length: 255)]
    private ?string $firstname = null;

    #[ORM\Column(length: 255)]
    private ?string $lastname = null;

    #[ORM\Column(length: 255)]
    private ?string $phone = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $formationsSouhaitees = null;

    /**
     * @var Collection<int, Formation>
     */
    #[ORM\ManyToMany(targetEntity: Formation::class, inversedBy: 'users')]
    #[ORM\JoinTable(name: 'user_formations')]
    private Collection $formations;

    /**
     * @var Collection<int, UserFormation>
     */
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: UserFormation::class)]
    private Collection $userFormations;

    /**
     * @var Collection<int, UserAnswer>
     */
    #[ORM\OneToMany(targetEntity: UserAnswer::class, mappedBy: 'user')]
    private Collection $userAnswers;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Progression::class, orphanRemoval: true)]
    private Collection $progressions;

    /**
     * @var Collection<int, ForumTopic>
     */
    #[ORM\OneToMany(targetEntity: ForumTopic::class, mappedBy: 'author')]
    private Collection $forumTopics;

    /**
     * @var Collection<int, ForumMessage>
     */
    #[ORM\OneToMany(targetEntity: ForumMessage::class, mappedBy: 'author')]
    private Collection $forumMessages;
    
    /**
     * @var Collection<int, Review>
     */
    #[ORM\OneToMany(targetEntity: Review::class, mappedBy: 'user')]
    private Collection $reviews;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: FeedbackFormation::class)]
    private Collection $feedbackFormations;

    public function __construct()
    {
        $this->userFormations = new ArrayCollection();
        $this->formations = new ArrayCollection();
        $this->userAnswers = new ArrayCollection();
        $this->progressions = new ArrayCollection();
        $this->forumTopics = new ArrayCollection();
        $this->forumMessages = new ArrayCollection();
        $this->reviews = new ArrayCollection();
        $this->feedbackFormations = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->firstname . ' ' . $this->lastname;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;
        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';
        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;
        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;
        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;
        return $this;
    }

    public function eraseCredentials(): void
    {
        // $this->plainPassword = null;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;
        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): static
    {
        $this->firstname = $firstname;
        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): static
    {
        $this->lastname = $lastname;
        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): static
    {
        $this->phone = $phone;
        return $this;
    }

    public function getFormationsSouhaitees(): ?string
    {
        return $this->formationsSouhaitees;
    }

    public function setFormationsSouhaitees(?string $formationsSouhaitees): self
    {
        $this->formationsSouhaitees = $formationsSouhaitees;
        return $this;
    }

    public function getFormations(): Collection
    {
        return $this->formations;
    }

    public function addFormation(Formation $formation): self
    {
        if (!$this->formations->contains($formation)) {
            $this->formations[] = $formation;
        }
        return $this;
    }

    public function removeFormation(Formation $formation): self
    {
        $this->formations->removeElement($formation);
        return $this;
    }

    /**
     * @return Collection<int, UserFormation>
     */
    public function getUserFormations(): Collection
    {
        return $this->userFormations;
    }

    public function addUserFormation(UserFormation $userFormation): self
    {
        if (!$this->userFormations->contains($userFormation)) {
            $this->userFormations[] = $userFormation;
            $userFormation->setUser($this);
        }
        return $this;
    }

    public function removeUserFormation(UserFormation $userFormation): self
    {
        if ($this->userFormations->removeElement($userFormation)) {
            if ($userFormation->getUser() === $this) {
                $userFormation->setUser(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection<int, UserAnswer>
     */
    public function getUserAnswers(): Collection
    {
        return $this->userAnswers;
    }

    public function addUserAnswer(UserAnswer $userAnswer): static
    {
        if (!$this->userAnswers->contains($userAnswer)) {
            $this->userAnswers->add($userAnswer);
            $userAnswer->setUser($this);
        }
        return $this;
    }

    public function removeUserAnswer(UserAnswer $userAnswer): static
    {
        if ($this->userAnswers->removeElement($userAnswer)) {
            if ($userAnswer->getUser() === $this) {
                $userAnswer->setUser(null);
            }
        }
        return $this;
    }

    public function getProgressions(): Collection
    {
        return $this->progressions;
    }


    /**
     * @return Collection<int, ForumTopic>
     */
    public function getForumTopics(): Collection
    {
        return $this->forumTopics;
    }

    public function addForumTopic(ForumTopic $forumTopic): static
    {
        if (!$this->forumTopics->contains($forumTopic)) {
            $this->forumTopics->add($forumTopic);
            $forumTopic->setAuthor($this);
        }
        return $this;
    }

    public function removeForumTopic(ForumTopic $forumTopic): static
    {
        if ($this->forumTopics->removeElement($forumTopic)) {
            if ($forumTopic->getAuthor() === $this) {
                $forumTopic->setAuthor(null);
            }
        }
        return $this;
    }

     /**
     * @return Collection<int, ForumMessage>
     */
    public function getForumMessages(): Collection
    {
        return $this->forumMessages;
    }

    public function addForumMessage(ForumMessage $forumMessage): static
    {
        if (!$this->forumMessages->contains($forumMessage)) {
            $this->forumMessages->add($forumMessage);
            $forumMessage->setAuthor($this);
        }
        return $this;
    }

    public function removeForumMessage(ForumMessage $forumMessage): static
    {
        if ($this->forumMessages->removeElement($forumMessage)) {
            if ($forumMessage->getAuthor() === $this) {
                $forumMessage->setAuthor(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection<int, Review>
     */
    public function getReviews(): Collection
    {
        return $this->reviews;
    }

    public function addReview(Review $review): static
    {
        if (!$this->reviews->contains($review)) {
            $this->reviews->add($review);
            $review->setUser($this);
        }
        return $this;
    }

    public function removeReview(Review $review): static
    {
        if ($this->reviews->removeElement($review)) {
            if ($review->getUser() === $this) {
                $review->setUser(null);
            }
        }
        return $this;
    }

    public function getFeedbackFormations(): Collection
    {
        return $this->feedbackFormations;
    }

    public function addFeedbackFormation(FeedbackFormation $feedback): static
    {
        if (!$this->feedbackFormations->contains($feedback)) {
            $this->feedbackFormations->add($feedback);
            $feedback->setUser($this);
        }
        return $this;
    }

    public function removeFeedbackFormation(FeedbackFormation $feedback): static
    {
        if ($this->feedbackFormations->removeElement($feedback)) {
            if ($feedback->getUser() === $this) {
                $feedback->setUser(null);
            }
        }
        return $this;
    }
}
