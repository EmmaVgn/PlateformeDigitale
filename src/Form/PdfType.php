<?php

namespace App\Form;

use App\Entity\Pdf;
use Symfony\Component\Form\AbstractType;
use Vich\UploaderBundle\Form\Type\VichFileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class PdfType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('imageFile', VichFileType::class, [
            'label' => 'Fichier (PDF, vidéo, PPT)',
            'required' => false,
            'allow_delete' => true,
            'download_uri' => true,
            'attr' => ['accept' => '.pdf, .ppt, .pptx, .mp4']
        ])
        ->add('title', TextType::class, [
            'label' => 'Titre du fichier',
            'required' => false,
        ])
        ->add('estimatedDuration', IntegerType::class, [
            'label' => 'Durée estimée (min)',
            'required' => false,
        ]);
}

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Pdf::class,
        ]);
    }
}