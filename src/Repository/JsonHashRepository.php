<?php

namespace App\Repository;

use App\Entity\JsonHash;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<JsonHash>
 *
 * @method JsonHash|null find($id, $lockMode = null, $lockVersion = null)
 * @method JsonHash|null findOneBy(array $criteria, array $orderBy = null)
 * @method JsonHash[]    findAll()
 * @method JsonHash[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class JsonHashRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, JsonHash::class);
    }

//    /**
//     * @return JsonHash[] Returns an array of JsonHash objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('j')
//            ->andWhere('j.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('j.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?JsonHash
//    {
//        return $this->createQueryBuilder('j')
//            ->andWhere('j.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
