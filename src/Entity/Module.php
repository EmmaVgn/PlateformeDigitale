<?php

namespace App\Entity;

use App\Repository\ModuleRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\HttpFoundation\File\File;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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
    #[Assert\File(
        maxSize: '100M',
        mimeTypes: ['application/pdf', 'application/vnd.ms-powerpoint', 'application/vnd.openxmlformats-officedocument.presentationml.presentation', 'video/mp4'],
        mimeTypesMessage: 'Veuillez uploader un fichier PDF, PPT, ou MP4 valide.',
        maxSizeMessage: 'Le fichier est trop lourd. Maximum 100 Mo autorisé.'
    )]
    private ?File $fileObj = null;

    #[ORM\Column(nullable: true)]
    private ?string $imageName = null;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\ManyToOne(inversedBy: 'modules')]
    private ?Formation $formation = null;

    #[ORM\OneToMany(mappedBy: 'modules', targetEntity: Pdf::class, cascade: ['persist'], orphanRemoval: true)]
    private Collection $pdfs;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $estimatedDuration = null;

    public function __construct()
    {
        $this->pdfs = new ArrayCollection();
    }

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

    public function getPdfs(): Collection
    {
        return $this->pdfs;
    }

    public function addPdf(Pdf $pdf): static
    {
        if (!$this->pdfs->contains($pdf)) {
            $this->pdfs[] = $pdf;
            $pdf->setModules($this);
        }

        return $this;
    }

    public function removePdf(Pdf $pdf): static
    {
        if ($this->pdfs->removeElement($pdf)) {
            if ($pdf->getModules() === $this) {
                $pdf->setModules(null);
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

    public function updateEstimatedDurationFromFiles(): void
    {
        $total = 0;

        foreach ($this->getPdfs() as $pdf) {
            if ($pdf->getEstimatedDuration()) {
                $total += $pdf->getEstimatedDuration();
            }
        }

        $this->setEstimatedDuration($total);
    }

}
