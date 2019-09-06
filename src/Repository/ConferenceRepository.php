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
    public function __construct(ManagerRegistry $registry)
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
    public function findNonVotedConf(array $votedIds)
    {
            //dd($votedIds);
            return $this->createQueryBuilder('v')
                ->andWhere('v.id NOT IN (:votedId)')
                ->setParameter('votedId', $votedIds)
                ->getQuery()
                ->getResult()
                ;
        }



    /**
     * @return Conference[]
     */
    public function searchConf($wordToSearch)
    {

        return $this->createQueryBuilder('c')
                        ->where('c.title LIKE "%":wordToSearch"%"')
                        ->setParameter('wordToSearch', $wordToSearch)
                        ->getQuery()
                        ->getResult()
        ;

       /* $conf = $this->getEntityManager()->getConnection();
        $sql = "SELECT *
            FROM conference c
            WHERE c.title LIKE '%':wordToSearch'%' ";
        $stmt = $conn->prepare($sql);
        $stmt->execute(['wordToSearch' => $wordToSearch]);
        return $stmt->fetchAll();*/
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
