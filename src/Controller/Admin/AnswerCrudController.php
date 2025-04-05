<?php

namespace App\Controller\Admin;

use App\Entity\Answer;
use App\Form\AnswerType;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormInterface;

class AnswerCrudController extends AbstractCrudController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public static function getEntityFqcn(): string
    {
        return Answer::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnDetail(),
            BooleanField::new('isCorrect'),
            AssociationField::new('question'),
        ];
    }

    // Utilisez le service EntityManagerInterface pour créer le formulaire
    public function createForm(string $entityFqcn, $entityId = null, array $context = []): FormInterface
    {
        // Si un entityId est fourni, nous recherchons l'entité correspondante dans la base de données
        $entity = $entityId ? $this->entityManager->getRepository($entityFqcn)->find($entityId) : new $entityFqcn();
        
        // Créer le formulaire en utilisant AnswerType pour personnaliser le formulaire
        $form = $this->createForm(AnswerType::class, $entity, $context);

        return $form;
    }
}
