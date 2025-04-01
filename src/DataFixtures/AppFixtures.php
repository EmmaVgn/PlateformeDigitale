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
        // ADMIN
        $admin = new User();
        $admin->setEmail('admin@example.com')
            ->setFirstname('Admin')
            ->setLastname('Test')
            ->setPhone('0101010101')
            ->setIsVerified(true)
            ->setRoles(['ROLE_ADMIN']);
        $admin->setPassword($this->hasher->hashPassword($admin, 'adminpass'));
        $manager->persist($admin);

        // USER
        $user = new User();
        $user->setEmail('user@example.com')
            ->setFirstname('Utilisateur')
            ->setLastname('Test')
            ->setPhone('0600000000')
            ->setIsVerified(true)
            ->setRoles(['ROLE_USER']);
        $user->setPassword($this->hasher->hashPassword($user, 'userpass'));
        $manager->persist($user);

        $formationsData = [
            ['title' => 'Maîtriser les bases de HTML & CSS', 'slug' => 'maitriser-les-bases-de-html-css', 'description' => 'Apprenez à créer des sites web modernes avec HTML et CSS.'],
            ['title' => 'Introduction à JavaScript moderne', 'slug' => 'introduction-a-javascript-moderne', 'description' => 'Un parcours pour comprendre les fondamentaux de JavaScript.'],
            ['title' => 'Créer son site avec WordPress', 'slug' => 'creer-son-site-avec-wordpress', 'description' => 'Utilisez WordPress pour créer un site facilement.'],
            ['title' => 'Développement PHP avancé', 'slug' => 'developpement-php-avance', 'description' => 'Approfondissez vos compétences en PHP.'],
            ['title' => 'Initiation à la cybersécurité', 'slug' => 'initiation-a-la-cybersecurite', 'description' => 'Sensibilisation aux bonnes pratiques en cybersécurité.'],
        ];
        
        foreach ($formationsData as $index => $data) {
            $formation = new Formation();
            $formation->setTitle($data['title'])
                ->setSlug($data['slug'])
                ->setDescription($data['description'])
                ->setIsPublished(true)
                ->setCreatedAt(new \DateTimeImmutable())
                ->setUpdatedAt(new \DateTimeImmutable());
            $manager->persist($formation);
        
            // Module
            $module = new Module();
            $module->setTitle("Module 1 - Introduction")
                ->setContent("Introduction au thème de la formation : " . $data['title'])
                ->setFile("module{$index}.pdf")
                ->setFormation($formation);
            $manager->persist($module);
        
            // Quiz
            $quiz = new Quiz();
            $quiz->setTitle("Quiz - " . $data['title'])
                ->setFormation($formation);
            $manager->persist($quiz);
        
            // Question
            $question = new Question();
            $question->setContent("Question d'introduction à la formation : " . $data['title'])
                ->setQuiz($quiz);
            $manager->persist($question);
        
            // Réponses
            foreach (['Bonne réponse', 'Fausse réponse 1', 'Fausse réponse 2'] as $i => $answerText) {
                $answer = new Answer();
                $answer->setContent($answerText)
                    ->setIsCorrect($i === 0)
                    ->setQuestion($question);
                $manager->persist($answer);
            }
        
            // Inscription utilisateur uniquement à la 1ère formation
            if ($index === 0) {
                $userFormation = new UserFormation();
                $userFormation->setUser($user)
                    ->setFormation($formation)
                    ->setProgression(0)
                    ->setIsCompleted(false);
                $manager->persist($userFormation);
            }
        }
        // Flush all data to the database
        $manager->flush();
    }
}
        