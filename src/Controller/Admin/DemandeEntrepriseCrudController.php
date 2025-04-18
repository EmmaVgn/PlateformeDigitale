<?php

namespace App\Controller\Admin;

use App\Entity\DemandeEntreprise;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class DemandeEntrepriseCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return DemandeEntreprise::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('nom'),
            EmailField::new('email'),
            TextField::new('entreprise'),
            TextEditorField::new('message'),
            DateTimeField::new('createdAt')->hideOnForm(),
        ];
    }
}
