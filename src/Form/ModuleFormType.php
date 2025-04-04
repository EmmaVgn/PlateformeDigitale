<?php 

// src/Form/ModuleType.php
namespace App\Form;

use App\Entity\Module;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Vich\UploaderBundle\Form\Type\VichFileType; // Import VichFileType
use Symfony\Component\OptionsResolver\OptionsResolver;

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
                'attr' => ['accept' => '.pdf, .ppt, .pptx, .mp4'],  // Restrict file types
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Module::class,
        ]);
    }
}
