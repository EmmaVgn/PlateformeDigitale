<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Formation;
use App\Entity\Module;
use App\Entity\Quiz;
use App\Entity\Question;
use App\Entity\Answer;
use App\Entity\UserFormation;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $hasher) {}

    public function load(ObjectManager $manager): void
    {
        // Création de l'admin
        $admin = new User();
        $admin->setEmail('admin@example.com')
            ->setFirstname('Admin')
            ->setLastname('Test')
            ->setPhone('0101010101')
            ->setIsVerified(true)
            ->setRoles(['ROLE_ADMIN']);
        $admin->setPassword($this->hasher->hashPassword($admin, 'adminpass'));
        $manager->persist($admin);

        // Création de l'utilisateur
        $user = new User();
        $user->setEmail('user@example.com')
            ->setFirstname('Utilisateur')
            ->setLastname('Test')
            ->setPhone('0600000000')
            ->setIsVerified(true)
            ->setRoles(['ROLE_USER']);
        $user->setPassword($this->hasher->hashPassword($user, 'userpass'));
        $manager->persist($user);

        // Définition des formations
        $formationsData = [
            [
                'title' => 'Maîtriser les bases de HTML & CSS',
                'slug' => 'maitriser-les-bases-de-html-css',
                'description' => 'Apprenez à créer des sites web modernes avec HTML et CSS.',
                'objectifDeFormation' => 'Apprendre les bases de la création de sites web.',
                'programme' => '1. Introduction à HTML 2. Apprendre les balises 3. Structuration du code',
                'modaliteAcces' => 'Accès en ligne 24/7',
                'modaliteEvaluation' => 'Évaluation finale via un projet',
                'coutEtFinancement' => 'Gratuit',
                'contact' => 'contact@formation.com',
                'accessibilite' => 'Accessible à tous',
                'publicCible' => 'Débutants en développement web',
                'preRequis' => 'Aucun',
                'duree' => '10 heures',
                'dateFormation' => 'Tout au long de l\'année',
                'lieu' => 'En ligne',
                'competences' => ['HTML', 'CSS', 'Web Design'],
            ],
            [
                'title' => 'Introduction à JavaScript moderne',
                'slug' => 'introduction-a-javascript-moderne',
                'description' => 'Un parcours pour comprendre les fondamentaux de JavaScript.',
                'objectifDeFormation' => 'Comprendre et appliquer les bases de JavaScript moderne.',
                'programme' => '1. Introduction aux variables et fonctions 2. Manipulation du DOM',
                'modaliteAcces' => 'Accès en ligne',
                'modaliteEvaluation' => 'QCM en fin de formation',
                'coutEtFinancement' => 'Gratuit',
                'contact' => 'contact@formation.com',
                'accessibilite' => 'Accessible à tous',
                'publicCible' => 'Développeurs débutants',
                'preRequis' => 'Connaissances de base en HTML et CSS',
                'duree' => '15 heures',
                'dateFormation' => 'Tout au long de l\'année',
                'lieu' => 'En ligne',
                'competences' => ['JavaScript', 'DOM Manipulation', 'Programmation'],
            ],
            [
                'title' => 'Création d\'applications web avec React',
                'slug' => 'creation-applications-web-avec-react',
                'description' => 'Développez des applications web interactives avec React.',
                'objectifDeFormation' => 'Apprenez à utiliser React pour créer des interfaces utilisateur dynamiques.',
                'programme' => '1. Introduction à React 2. Gestion de l\'état avec Redux 3. Gestion des événements',
                'modaliteAcces' => 'Accès en ligne, ressources vidéo',
                'modaliteEvaluation' => 'Projet pratique à la fin du cours',
                'coutEtFinancement' => '100€',
                'contact' => 'support@formation.com',
                'accessibilite' => 'Accessible aux personnes ayant des bases en JavaScript',
                'publicCible' => 'Développeurs front-end',
                'preRequis' => 'Connaissance de JavaScript',
                'duree' => '20 heures',
                'dateFormation' => 'Démarrage toutes les 4 semaines',
                'lieu' => 'En ligne',
                'competences' => ['React', 'JavaScript', 'Redux'],
            ],
            [
                'title' => 'Développement mobile avec Flutter',
                'slug' => 'developpement-mobile-avec-flutter',
                'description' => 'Apprenez à développer des applications mobiles natives avec Flutter.',
                'objectifDeFormation' => 'Maîtriser Flutter pour développer des applications mobiles pour Android et iOS.',
                'programme' => '1. Introduction à Flutter 2. Création d\'interfaces mobiles 3. Intégration de services backend',
                'modaliteAcces' => 'Accès en ligne, cours avec supports',
                'modaliteEvaluation' => 'Test en ligne et évaluation pratique',
                'coutEtFinancement' => '150€',
                'contact' => 'info@formation.com',
                'accessibilite' => 'Formation en ligne accessible à tous',
                'publicCible' => 'Développeurs mobiles',
                'preRequis' => 'Connaissances de base en programmation',
                'duree' => '30 heures',
                'dateFormation' => 'Prochain démarrage en mars',
                'lieu' => 'En ligne',
                'competences' => ['Flutter', 'Dart', 'Développement mobile'],
            ],
        ];

        foreach ($formationsData as $data) {
            $formation = new Formation();
            $formation->setTitle($data['title'])
                ->setSlug($data['slug'])
                ->setDescription($data['description'])
                ->setObjectifDeFormation($data['objectifDeFormation'])
                ->setProgramme($data['programme'])
                ->setModaliteAcces($data['modaliteAcces'])
                ->setModaliteEvaluation($data['modaliteEvaluation'])
                ->setCoutEtFinancement($data['coutEtFinancement'])
                ->setContact($data['contact'])
                ->setAccessibilite($data['accessibilite'])
                ->setPublicCible($data['publicCible'])
                ->setPreRequis($data['preRequis'])
                ->setDuree($data['duree'])
                ->setDateFormation($data['dateFormation'])
                ->setLieu($data['lieu'])
                ->setCompetences(implode(', ', $data['competences']))  // Implémentation des compétences comme une chaîne de caractères
                ->setIsPublished(true)
                ->setCreatedAt(new \DateTimeImmutable())
                ->setUpdatedAt(new \DateTimeImmutable());
            $manager->persist($formation);

            // Création de 2 modules
            for ($i = 1; $i <= 2; $i++) {
                $module = new Module();
                
                // Set the basic module properties
                $module->setTitle("Module $i - Introduction")
                       ->setContent("Contenu du module $i pour la formation : " . $data['title'])
                       ->setFormation($formation);
                
                // Manually create a file object if you want to simulate the upload
                $file = new \Symfony\Component\HttpFoundation\File\File('path/to/your/files/module' . $i . '.pdf');
                
                // Set the file object to the module (this triggers the upload mechanism)
                $module->setFileObj($file);
                
                // Optionally, set the file path manually if needed (this might be unnecessary if using the file object)
                // Removed the call to setFile as the method does not exist in the Module entity
                
                $manager->persist($module);
            }
            

            // Création de 2 quiz
            for ($i = 1; $i <= 2; $i++) {
                $quiz = new Quiz();
                $quiz->setTitle("Quiz $i - " . $data['title'])
                    ->setFormation($formation);
                $manager->persist($quiz);

                // Création de 3 questions par quiz
                for ($j = 1; $j <= 3; $j++) {
                    $question = new Question();
                    $question->setContent("Question $j pour le quiz $i de la formation : " . $data['title'])
                        ->setQuiz($quiz);
                    $manager->persist($question);

                    // Réponses pour chaque question
                    foreach (['Bonne réponse', 'Fausse réponse 1', 'Fausse réponse 2'] as $index => $answerText) {
                        $answer = new Answer();
                        $answer->setContent($answerText)
                            ->setIsCorrect($index === 0)
                            ->setQuestion($question);
                        $manager->persist($answer);
                    }
                }
            }

            // Inscription utilisateur uniquement à la 1ère formation
            if ($data['title'] === 'Maîtriser les bases de HTML & CSS') {
                $userFormation = new UserFormation();
                $userFormation->setUser($user)
                    ->setFormation($formation)
                    ->setProgression(0)
                    ->setIsCompleted(false)
                    ->setIsValidated(true);
                $manager->persist($userFormation);
            }
        }

        $manager->flush();
    }
}
