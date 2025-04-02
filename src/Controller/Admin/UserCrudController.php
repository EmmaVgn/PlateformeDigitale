<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Entity\Formation;
use App\Entity\UserFormation;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TelephoneField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\PasswordField;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnIndex(),
            TextField::new('firstname', 'Prénom'),
            TextField::new('lastname', 'Nom'),
            EmailField::new('email'),
            TelephoneField::new('phone', 'Téléphone'),
            
            BooleanField::new('isVerified', 'Email vérifié'),
            ArrayField::new('roles')->onlyOnIndex(),
            TextField::new('plainPassword', 'Mot de passe :')
                ->onlyWhenCreating()->setRequired(true)
                ->setFormTypeOptions(['attr' => ['placeholder' => 'Mot de passe de l\'utilisateur']])
                ->hideOnIndex(),// Champ visible uniquement lors de la création de l'utilisateur
            AssociationField::new('formations') // Utilisez "formations" pour gérer la relation ManyToMany
                ->setLabel('Formations')
                ->setFormTypeOption('multiple', true)  // Permet de sélectionner plusieurs formations
                ->setFormTypeOption('by_reference', false) // Permet de gérer les relations entre User et Formation
                ->setHelp('Sélectionnez les formations auxquelles cet utilisateur peut accéder'),
        ];
    }

    /**
     * Cette méthode est appelée après la persistance de l'entité User.
     */
    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if ($entityInstance instanceof User) {
            // Récupérer les formations sélectionnées pour l'utilisateur
            $formations = $entityInstance->getFormations();

            // Pour chaque formation, créer un enregistrement dans la table de jointure `UserFormation`
            foreach ($formations as $formation) {
                // Crée une nouvelle instance de UserFormation
                $userFormation = new UserFormation();
                $userFormation->setUser($entityInstance);  // L'utilisateur est celui que nous sommes en train d'ajouter
                $userFormation->setFormation($formation);  // La formation est celle qui a été sélectionnée
                $userFormation->setProgression(0); // Par exemple, on peut initialiser la progression à 0
                $userFormation->setIsCompleted(false); // Par défaut, on peut initialiser à false
                $userFormation->setIsValidated(false); // Par défaut, la validation n'est pas encore faite

                // Persist la relation dans la table user_formations
                $entityManager->persist($userFormation);
            }
        }

        // Appeler la méthode parente pour effectuer l'enregistrement classique
        parent::persistEntity($entityManager, $entityInstance);
    }
}
