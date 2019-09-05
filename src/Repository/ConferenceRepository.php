<?php

namespace App\Repository;

use App\Entity\Conference;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManager;

/**
 * @method Conference|null find($id, $lockMode = null, $lockVersion = null)
 * @method Conference|null findOneBy(array $criteria, array $orderBy = null)
 * @method Conference[]    findAll()
 * @method Conference[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ConferenceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, EntityManager $entityManager)
    {
        parent::__construct($registry, Conference::class);
    }

    // /**
    //  * @return Conferences[] Returns an array of Conferences objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /**
     * @return Conference[]
     */
    public function findNonVotedConf($votedIds)
    {
        foreach ($votedIds as $votedId){
            return $this->createQueryBuilder('v')
                ->andWhere('v.id != :votedId')
                ->setParameter('votedId', $votedId)
                ->getQuery()
                ->getResult()
                ;
        }

    }

    /**
     * @return Conference[]
     */
    public function searchConf($wordToSearch)
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT *
            FROM conference c
            WHERE c.title LIKE '%':wordToSearch'%' ";
        $stmt = $conn->prepare($sql);
        $stmt->execute(['wordToSearch' => $wordToSearch]);
    }
    /*
    public function findOneBySomeField($value): ?Conferences
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
