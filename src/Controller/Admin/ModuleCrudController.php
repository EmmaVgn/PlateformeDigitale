<?php

namespace App\Controller\Admin;

use App\Form\PdfType;
use App\Entity\Module;
use Doctrine\ORM\EntityManagerInterface;

use Vich\UploaderBundle\Form\Type\VichFileType;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class ModuleCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Module::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('title'),
            TextEditorField::new('content'),
            AssociationField::new('formation')->setLabel('Formation liée'),

            // ⬇️ Téléversement du fichier unique (optionnel si tu utilises uniquement les fichiers multiples)
            FormField::addPanel('Téléversement principal (optionnel)')->setIcon('fa fa-file-upload'),
            TextField::new('fileObj')
                ->setFormType(VichFileType::class)
                ->setFormTypeOption('attr', [
                    'accept' => 'application/pdf, application/vnd.ms-powerpoint, video/mp4'
                ])
                ->setLabel('Fichier unique'),

            // ⬇️ Liste des fichiers multiples associés (PDF, vidéos, PPT...)
            FormField::addPanel('Fichiers associés au module')->setIcon('fa fa-folder-open'),
            CollectionField::new('pdfs')
                ->setLabel('Fichiers du module')
                ->setEntryType(PdfType::class)
                ->allowAdd()
                ->allowDelete()
                ->setFormTypeOption('by_reference', false)
                ->renderExpanded(),
        ];
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof Module) return;
    
        // 🔁 Calcul de la durée automatique
        $entityInstance->updateEstimatedDurationFromFiles();
    
        $formation = $entityInstance->getFormation();
        if ($formation) {
            $formation->updateTotalDuration();
            $entityManager->persist($formation);
        }
    
        parent::persistEntity($entityManager, $entityInstance);
    }
    
    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof Module) return;
    
        // 🔁 Calcul de la durée automatique
        $entityInstance->updateEstimatedDurationFromFiles();
    
        $formation = $entityInstance->getFormation();
        if ($formation) {
            $formation->updateTotalDuration();
            $entityManager->persist($formation);
        }
    
        parent::updateEntity($entityManager, $entityInstance);
    }
    
}


