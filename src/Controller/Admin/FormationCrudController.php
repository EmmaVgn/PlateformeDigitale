<?php

namespace App\Controller\Admin;

use App\Entity\Formation;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class FormationCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Formation::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('title'),
            TextareaField::new('description'),
            BooleanField::new('isPublished'),
            TextEditorField::new('objectifDeFormation'),
            TextEditorField::new('programme'),
            TextEditorField::new('competences'),
            TextField::new('modaliteAcces'),
            TextField::new('modaliteEvaluation'),
            TextField::new('coutEtFinancement'),
            TextField::new('contact'),
            TextField::new('accessibilite'),
            TextEditorField::new('publicCible'),
            TextField::new('preRequis'),
            TextField::new('duree'),
            TextField::new('dateFormation'),
            TextField::new('lieu'),
            DateTimeField::new('createdAt')->hideOnForm(),
            DateTimeField::new('updatedAt')->hideOnForm(),
            AssociationField::new('users') // Permet de gérer la relation inverse
                ->setFormTypeOption('multiple', true)
                ->setFormTypeOption('by_reference', false) // Permet de gérer la relation entre User et Formation
                ->setHelp('Sélectionnez les utilisateurs qui participent à cette formation'),
            AssociationField::new('modules') // Permet de gérer la relation inverse
                ->setFormTypeOption('multiple', true)
                ->setFormTypeOption('by_reference', false) // Permet de gérer la relation entre Module et Formation
                ->setHelp('Sélectionnez les modules associés à cette formation'),
            AssociationField::new('quizzes') // Cette ligne permet d'ajouter une association avec les quiz
                ->setFormTypeOptions([
                    'by_reference' => false, // Permet de lier plusieurs quiz à une formation
                    'multiple' => true, // Permet d'associer plusieurs quiz
                    'required' => false, // Optionnel : mettre à true si vous voulez que ce champ soit obligatoire
                ])
        ];
    }
}
