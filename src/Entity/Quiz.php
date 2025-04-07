<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\QuizRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * @ORM\HasLifecycleCallbacks
 */
#[ORM\Entity(repositoryClass: QuizRepository::class)]
class Quiz
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $slug = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\ManyToOne(inversedBy: 'quizzes')]
    private ?Formation $formation = null;

    /**
     * @var Collection<int, Question>
     */
    #[ORM\OneToMany(targetEntity: Question::class, mappedBy: 'quiz', cascade: ['persist', 'remove'])]
    private Collection $questions;

    /**
     * @var Collection<int, UserAnswer>
     */
    #[ORM\OneToMany(targetEntity: UserAnswer::class, mappedBy: 'quiz')]
    private Collection $userAnswers;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $estimatedDuration = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $pointSystem = null;
    


    public function __construct()
    {
        $this->questions = new ArrayCollection();
        $this->userAnswers = new ArrayCollection();
    }

     /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->title ?? 'Quiz non définie';  // Retourne le titre ou un texte par défaut
    }

        /**
     * @ORM\PrePersist
     */
    public function setSlugAutomatically()
    {
        if (!$this->slug) {
            $this->slug = strtolower(str_replace(' ', '-', $this->title));
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;
        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
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

    public function getFormation(): ?Formation
    {
        return $this->formation;
    }

    public function setFormation(?Formation $formation): static
    {
        $this->formation = $formation;

        return $this;
    }

    /**
     * @return Collection<int, Question>
     */
    public function getQuestions(): Collection
    {
        return $this->questions;
    }

    public function addQuestion(Question $question): static
    {
        if (!$this->questions->contains($question)) {
            $this->questions->add($question);
            $question->setQuiz($this);
        }

        return $this;
    }

    public function removeQuestion(Question $question): static
    {
        if ($this->questions->removeElement($question)) {
            // set the owning side to null (unless already changed)
            if ($question->getQuiz() === $this) {
                $question->setQuiz(null);
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
            $userAnswer->setQuiz($this);
        }

        return $this;
    }

    public function removeUserAnswer(UserAnswer $userAnswer): static
    {
        if ($this->userAnswers->removeElement($userAnswer)) {
            // set the owning side to null (unless already changed)
            if ($userAnswer->getQuiz() === $this) {
                $userAnswer->setQuiz(null);
            }
        }

        return $this;
    }

    public function getEstimatedDuration(): ?int
    {
        return $this->estimatedDuration;
    }

    public function setEstimatedDuration(?int $estimatedDuration): self
    {
        $this->estimatedDuration = $estimatedDuration;
        return $this;
    }
    public function updateEstimatedDuration(): void
    {
        // Par exemple : 1,5 minute par question
        $this->estimatedDuration = ceil(count($this->questions) * 1.5);
    }

    public function getPointSystem(): ?string
    {
        return $this->pointSystem;
    }

    public function setPointSystem(?string $pointSystem): static
    {
        $this->pointSystem = $pointSystem;
        return $this;
    }

}
