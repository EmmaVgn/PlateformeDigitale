<?php

namespace App\Repository;

use App\Entity\Formation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Formation>
 *
 * @method Formation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Formation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Formation[]    findAll()
 * @method Formation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FormationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Formation::class);
    }

    /**
     * Récupère les formations publiées, triées par date de création
     */
    public function findPublished(?int $limit = null): array
    {
        $qb = $this->createQueryBuilder('f')
            ->andWhere('f.isPublished = :val')
            ->setParameter('val', true)
            ->orderBy('f.createdAt', 'DESC');

        if ($limit) {
            $qb->setMaxResults($limit);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * Récupère une formation par son slug (et publiée)
     */
    public function findOnePublishedBySlug(string $slug): ?Formation
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.slug = :slug')
            ->andWhere('f.isPublished = true')
            ->setParameter('slug', $slug)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
