<?php

namespace App\Controller\Admin;

use App\Entity\UserFormation;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class UserFormationCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return UserFormation::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            // Afficher l'utilisateur (et ses propriétés)
            AssociationField::new('user')
                ->setLabel('Utilisateur')
                ->formatValue(function ($value) {
                    return $value ? $value->getFirstname() . ' ' . $value->getLastname() : 'N/A'; // Afficher le prénom et nom de l'utilisateur
                }),

            // Afficher la formation
            AssociationField::new('formation') // Sélectionner la formation
                ->setLabel('Formation')
                ->setFormTypeOption('by_reference', false)
                ->setFormTypeOption('multiple', true), // Permet de sélectionner plusieurs formations à l'utilisateur

            IntegerField::new('progression')->setLabel('Progression (%)'),
            BooleanField::new('isCompleted')->setLabel('Terminée ?'),
            DateTimeField::new('dateInscription')->setLabel('Date d’inscription')->hideOnForm(),
            BooleanField::new('isValidated')->setLabel('Validée par admin'),
        ];
    }
}
