<?php

namespace App\Controller\Admin;

use App\Entity\Quiz;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\String\Slugger\SluggerInterface;
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
    public function __construct(private SluggerInterface $slugger) {}


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
    
        // Génération du slug si vide
        if (!$entityInstance->getSlug()) {
            $slug = strtolower($this->slugger->slug($entityInstance->getTitle()));
            $entityInstance->setSlug($slug);
        }
    
        // Mise à jour de la durée estimée
        $entityInstance->updateEstimatedDuration();

        if (!$entityInstance instanceof Quiz) return;

        // 🔁 Rattache les questions sélectionnées au quiz
        foreach ($entityInstance->getQuestions() as $question) {
            $question->setQuiz($entityInstance);
            $entityManager->persist($question);
        }
    
        parent::persistEntity($entityManager, $entityInstance);
    }
    

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof Quiz) return;

        // Optionnel : regénère le slug si vide
        if (!$entityInstance->getSlug()) {
            $slug = strtolower($this->slugger->slug($entityInstance->getTitle()));
            $entityInstance->setSlug($slug);
        }

        $entityInstance->updateEstimatedDuration();

        if (!$entityInstance instanceof Quiz) return;

    foreach ($entityInstance->getQuestions() as $question) {
        $question->setQuiz($entityInstance);
        $entityManager->persist($question);
    }
    
        parent::updateEntity($entityManager, $entityInstance);
    }


}
