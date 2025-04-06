<?php

namespace App\Repository;

use App\Entity\Quiz;
use App\Entity\User;
use App\Entity\Formation;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Quiz>
 */
class QuizRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Quiz::class);
    }

    /**
     * Récupère tous les quiz liés à une formation donnée
     */
    public function findByFormation(int $formationId): array
    {
        return $this->createQueryBuilder('q')
            ->andWhere('q.formation = :id')
            ->setParameter('id', $formationId)
            ->orderBy('q.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findCompletedByUserAndFormation(User $user, Formation $formation): array
    {
        return $this->createQueryBuilder('q')
            ->innerJoin('q.userAnswers', 'ua')
            ->andWhere('ua.user = :user')
            ->andWhere('q.formation = :formation')
            ->setParameter('user', $user)
            ->setParameter('formation', $formation)
            ->groupBy('q.id')
            ->getQuery()
            ->getResult();
    }

}
