<?php

namespace App\Repository;

use App\Entity\DataHash;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DataHash>
 *
 * @method DataHash|null find($id, $lockMode = null, $lockVersion = null)
 * @method DataHash|null findOneBy(array $criteria, array $orderBy = null)
 * @method DataHash[]    findAll()
 * @method DataHash[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DataHashRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DataHash::class);
    }

//    /**
//     * @return DataHash[] Returns an array of DataHash objects
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

//    public function findOneBySomeField($value): ?DataHash
//    {
//        return $this->createQueryBuilder('j')
//            ->andWhere('j.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
