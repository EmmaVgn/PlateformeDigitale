<?php

namespace App\Controller\Admin;

use App\Entity\Review;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;

class ReviewCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Review::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            AssociationField::new('user', 'Utilisateur'),
            AssociationField::new('formation', 'Formation'),
            IntegerField::new('rating', 'Note')->setColumns(2),
            TextareaField::new('comment', 'Commentaire'),
            BooleanField::new('isValidated', 'Validé'),
            DateTimeField::new('createdAt', 'Soumis le')->hideOnForm(),
        ];
    }
}
