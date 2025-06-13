<?php

namespace App\Repository;

use App\Entity\ReaccionComentario;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ReaccionComentario>
 *
 * @method ReaccionComentario|null find($id, $lockMode = null, $lockVersion = null)
 * @method ReaccionComentario|null findOneBy(array $criteria, array $orderBy = null)
 * @method ReaccionComentario[]    findAll()
 * @method ReaccionComentario[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReaccionComentarioRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ReaccionComentario::class);
    }

//    /**
//     * @return ReaccionComentario[] Returns an array of ReaccionComentario objects
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

//    public function findOneBySomeField($value): ?ReaccionComentario
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
