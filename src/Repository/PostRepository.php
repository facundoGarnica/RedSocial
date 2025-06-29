<?php

namespace App\Repository;

use App\Entity\Post;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Post>
 *
 * @method Post|null find($id, $lockMode = null, $lockVersion = null)
 * @method Post|null findOneBy(array $criteria, array $orderBy = null)
 * @method Post[]    findAll()
 * @method Post[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Post::class);
    }
    public function findAllWithUsersAndComments(): array
    {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.usuario', 'u')
            ->addSelect('u')
            ->leftJoin('p.comentarios', 'c')
            ->addSelect('c')
            ->leftJoin('c.usuario', 'cu')
            ->addSelect('cu')
            ->where('p.estado = :estado')
            ->setParameter('estado', 'aprobado')
            ->orderBy('p.FechaCreacion', 'DESC')
            ->getQuery()
            ->getResult();
    }
    public function getReaccionesPorPost(Post $post): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
            SELECT emoticon, COUNT(*) as cantidad
            FROM reaccion_post
            WHERE post_id = :postId
            GROUP BY emoticon
        ';
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery(['postId' => $post->getId()]);

        $resultados = [];
        foreach ($resultSet->fetchAllAssociative() as $row) {
            $resultados[$row['emoticon']] = (int)$row['cantidad'];
        }

        return $resultados;
    }

//    /**
//     * @return Post[] Returns an array of Post objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Post
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
