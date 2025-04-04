<?php

namespace App\Controller\Admin;

use App\Entity\Module;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use Vich\UploaderBundle\Form\Type\VichFileType;

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
            
            // Ajout d'un panneau pour le téléversement du fichier
            FormField::addPanel('Téléversement de fichier')->setIcon('fa fa-file-upload'),

            // The imageName is a string field, no need to set VichFileType here.

            // Use VichFileType for file upload handling
            TextField::new('fileObj')  // Handling the actual file upload with VichFileType
                ->setFormType(VichFileType::class)
                ->setFormTypeOption('attr', [
                    'accept' => 'application/pdf, application/vnd.ms-powerpoint, video/mp4'  // Allowed file types
                ])
                ->setLabel('Télécharger un fichier'),
        ];
    }
}
