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

        // FORMATION
        $formation = new Formation();
        $formation->setTitle('Débuter avec Symfony')
            ->setSlug('debuter-avec-symfony')
            ->setDescription('Une formation d’introduction à Symfony pour les débutants.')
            ->setIsPublished(true)
            ->setCreatedAt(new \DateTimeImmutable())
            ->setUpdatedAt(new \DateTimeImmutable());
        $manager->persist($formation);

        // MODULES
        $module1 = (new Module())
            ->setTitle("Introduction")
            ->setContent("Présentation de Symfony")
            ->setFile("intro.pdf")
            ->setFormation($formation);
        $manager->persist($module1);

        // QUIZ
        $quiz = (new Quiz())
            ->setTitle("Quiz de démarrage")
            ->setFormation($formation);
        $manager->persist($quiz);

        // QUESTION + RÉPONSES
        $question = (new Question())
            ->setContent("Symfony est un...")
            ->setQuiz($quiz);
        $manager->persist($question);

        foreach (['Framework PHP', 'Navigateur Web', 'Base de données'] as $i => $text) {
            $answer = (new Answer())
                ->setContent($text)
                ->setIsCorrect($i === 0)
                ->setQuestion($question);
            $manager->persist($answer);
        }

        // INSCRIPTION
        $userFormation = (new UserFormation())
            ->setUser($user)
            ->setFormation($formation)
            ->setProgression(0)
            ->setIsCompleted(false);
        $manager->persist($userFormation);

        $manager->flush();
    }
}