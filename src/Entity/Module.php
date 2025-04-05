<?php

namespace App\Entity;

use App\Repository\ModuleRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ModuleRepository::class)]
#[Vich\Uploadable]
class Module
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $content = null;

    #[Vich\UploadableField(mapping: 'module_file', fileNameProperty: 'imageName')]
    private ?File $fileObj = null;  // Change to handle file uploads for PDF/other files

    #[ORM\Column(nullable: true)]
    private ?string $imageName = null;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\ManyToOne(inversedBy: 'modules')]
    private ?Formation $formation = null;

   /**
    * @return string
    */
   public function __toString(): string
   {
       return $this->title ?? 'Module non définie';  // Retourne le titre ou un texte par défaut
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

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;
        return $this;
    }

    public function getFileObj(): ?File
    {
        return $this->fileObj;
    }

    public function setFileObj(?File $fileObj = null): void
    {
        $this->fileObj = $fileObj;
        if ($fileObj) {
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getImageName(): ?string
    {
        return $this->imageName;
    }

    public function setImageName(?string $imageName): void
    {
        $this->imageName = $imageName;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): static
    {
        $this->updatedAt = $updatedAt;
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
}
