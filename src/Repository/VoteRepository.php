<?php

namespace App\Repository;

use App\Entity\vote;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\Expr\Func;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\ResultSetMapping;

/**
 * @extends ServiceEntityRepository<Vote>
 *
 * @method Vote|null find($id, $lockMode = null, $lockVersion = null)
 * @method Vote|null findOneBy(array $criteria, array $orderBy = null)
 * @method Vote[]    findAll()
 * @method Vote[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VoteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, Vote::class);
        $this->entityManager = $entityManager;
    }
    public function getMonthlyYearlyVotes()
    {
        $currentMonth = (int) date('m');
        $currentYear = (int) date('Y');

        $sql = "SELECT COUNT(*) AS vote_count FROM vote WHERE MONTH(date_SV) = :month AND YEAR(date_SV) = :year";
        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('vote_count', 'voteCount');
        $query = $this->entityManager->createNativeQuery($sql, $rsm);
        $query->setParameter('month', $currentMonth);
        $query->setParameter('year', $currentYear);

        $result = $query->getSingleResult();

        return $result['voteCount'];
    }

    public function getTotalVotes()
    {
        $sql = "SELECT COUNT(*) AS vote_count FROM vote";
        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('vote_count', 'voteCount');
        $query = $this->entityManager->createNativeQuery($sql, $rsm);

        $result = $query->getSingleResult();

        return $result['voteCount'];
    }

    public function getLatestVotes()
    {
        $currentTime = new \DateTime();
        $twentyFourHoursAgo = (new \DateTime())->modify('-24 hours');

        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->select('v')
                     ->from('App\Entity\vote', 'v')
                     ->where('v.date_SV BETWEEN :start AND :end')
                     ->setParameter('start', $twentyFourHoursAgo)
                     ->setParameter('end', $currentTime)
                     ->orderBy('v.date_SV', 'DESC')
                     ->setMaxResults(10);

        $latestVotes = $queryBuilder->getQuery()->getResult();

        return $latestVotes;
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Vote $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(vote $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }
    public function findByDescE($description)
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.desc_E LIKE :description')
            ->setParameter('description', '%' . $description . '%')
            ->getQuery()
            ->getResult();
    }
   

    // /**
    //  * @return Vote[] Returns an array of Vote objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('v.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Vote
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
