<?php

namespace App\Controller\Admin;

use App\Entity\Formation;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
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
            TextField::new('objectifDeFormation'),
            TextField::new('programme'),
            TextField::new('modaliteAcces'),
            TextField::new('modaliteEvaluation'),
            TextField::new('coutEtFinancement'),
            TextField::new('contact'),
            TextField::new('accessibilite'),
            TextField::new('publicCible'),
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
        ];
    }
}
