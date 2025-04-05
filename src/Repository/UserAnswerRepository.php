<?php

namespace App\Repository;

use App\Entity\UserAnswer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserAnswer>
 */
class UserAnswerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserAnswer::class);
    }

    public function findByUserAndQuiz($user, $quiz)
    {
        return $this->createQueryBuilder('ua')
            ->andWhere('ua.user = :user')
            ->andWhere('ua.quiz = :quiz')
            ->setParameter('user', $user)
            ->setParameter('quiz', $quiz)
            ->getQuery()
            ->getResult();
    }
}
