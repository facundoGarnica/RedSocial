<?php

namespace App\Repository;

use App\Entity\ReaccionPost;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ReaccionPost>
 *
 * @method ReaccionPost|null find($id, $lockMode = null, $lockVersion = null)
 * @method ReaccionPost|null findOneBy(array $criteria, array $orderBy = null)
 * @method ReaccionPost[]    findAll()
 * @method ReaccionPost[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReaccionPostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ReaccionPost::class);
    }

//    /**
//     * @return ReaccionPost[] Returns an array of ReaccionPost objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('r.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?ReaccionPost
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
