<?php

namespace App\Entity;

use App\Repository\PdfRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: PdfRepository::class)]
#[Vich\Uploadable]
class Pdf
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Vich\UploadableField(mapping: 'pdf_file', fileNameProperty: 'imageName')]
    #[Assert\File(
        maxSize: '100M',
        maxSizeMessage: 'Le fichier est trop lourd. Taille maximale autorisée : {{ limit }}.'
    )]
    private ?File $imageFile = null;  // Use a separate field for the file upload

    #[ORM\Column(nullable: true)]
    private ?string $imageName = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\ManyToOne(inversedBy: 'pdfs')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Module $modules = null;

    // Getter and setter methods

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function setImageFile(?File $imageFile = null): void
    {
        $this->imageFile = $imageFile;
        if (null !== $imageFile) {
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getImageName(): ?string
    {
        return $this->imageName;
    }

    public function setImageName(string $imageName): void
    {
        $this->imageName = $imageName;
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

    public function getModules(): ?Module
    {
        return $this->modules;
    }

    public function setModules(?Module $modules): static
    {
        $this->modules = $modules;
        return $this;
    }
}
