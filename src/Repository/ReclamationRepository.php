<?php

namespace App\Repository;

use App\Entity\reclamation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityManagerInterface;


/**
 * @extends ServiceEntityRepository<Reclamation>
 *
 * @method Reclamation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Reclamation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Reclamation[]    findAll()
 * @method Reclamation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReclamationRepository extends ServiceEntityRepository
{
    private $entityManager;
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reclamation::class);
        $this->entityManager = $this->getEntityManager();

    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Reclamation $entity, bool $flush = true): void
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
    public function remove(Reclamation $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // /**
    //  * @return Reclamation[] Returns an array of Reclamation objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Reclamation
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    public function countByStatus(): array
    {
        return $this->createQueryBuilder('r')
            ->select('r.status_reclamation as status, COUNT(r.id_reclamation) as count')
            ->groupBy('r.status_reclamation')
            ->getQuery()
            ->getResult();
    }

    public function countByDate(): array
    {
    $connection = $this->getEntityManager()->getConnection();
    $sql = '
        SELECT DATE(date_reclamation) as date, COUNT(id_reclamation) as count
        FROM reclamation
        GROUP BY DATE(date_reclamation)
    ';
    $statement = $connection->executeQuery($sql);

    return $statement->fetchAllAssociative();
    }
public function countByMonth(): array
    {
        $connection = $this->getEntityManager()->getConnection();
        $sql = '
            SELECT MONTH(date_reclamation) as month, COUNT(id_reclamation) as count
            FROM reclamation
            GROUP BY MONTH(date_reclamation)
        ';
        $statement = $connection->executeQuery($sql);

        return $statement->fetchAllAssociative();
    }
    public function findReclamationsByUserId(int $userId): array
    {
        return $this->createQueryBuilder('r')
            ->where('r.id_user = :userId')
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getResult();
    }
    public function findReclamationsByDate(int $userId): array
    {
    return $this->createQueryBuilder('r')
        ->where('r.id_user = :userId')
        ->setParameter('userId', $userId)
        ->orderBy('r.date_reclamation', 'ASC')
        ->getQuery()
        ->getResult();
    }

}
