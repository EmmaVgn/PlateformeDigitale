<?php

namespace App\Controller\Admin;

use App\Entity\MessageContact;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;

class MessageContactCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return MessageContact::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('name')->setLabel('Nom'),
            EmailField::new('email'),
            TextareaField::new('message')->hideOnIndex(),
            DateTimeField::new('createdAt')->setLabel('Reçu le')->hideOnForm(),
        ];
    }
}
