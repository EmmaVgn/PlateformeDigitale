<?php

namespace App\Controller\Admin;

use App\Entity\Quiz;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class QuizCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Quiz::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            
            TextField::new('title'),
            AssociationField::new('questions'), // Associer plusieurs questions au quiz
            IntegerField::new('estimatedDuration')->setLabel('Durée estimée (min)')

        ];
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof Quiz) return;

        $entityInstance->updateEstimatedDuration();
        parent::persistEntity($entityManager, $entityInstance);
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof Quiz) return;

        $entityInstance->updateEstimatedDuration();
        parent::updateEntity($entityManager, $entityInstance);
    }

}
