<?php 

// src/Form/ModuleType.php
namespace App\Form;

use App\Form\PdfType;
use App\Entity\Module;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Vich\UploaderBundle\Form\Type\VichFileType; // Import VichFileType

class ModuleFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Title',
            ])
            ->add('content', TextareaType::class, [
                'label' => 'Content',
            ])
            ->add('fileObj', VichFileType::class, [
                'label' => 'Upload File (PDF, PPT, MP4)',
                'mapped' => true,  // Map the field to the entity property
                'required' => false,
                'allow_delete' => true,  // Allows deleting the file
                'download_uri' => true,  // Allows downloading the file
                'attr' => ['accept' => '.pdf, .ppt, .pptx, .mp4'] 
            ]) // Restrict file types
            ->add('pdfs', CollectionType::class, [
                'entry_type' => PdfType::class,
                'allow_add' => true,
                'by_reference' => false,
                'label' => false,
            ])
            ->add('estimatedDuration', IntegerType::class, [
                'label' => 'Durée estimée (en minutes)',
                'required' => false,
                'attr' => ['min' => 1],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Module::class,
        ]);
    }
}
