<?php

namespace App\Repository;

use App\Entity\evenement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @extends ServiceEntityRepository<Evenement>
 *
 * @method Evenement|null find($id, $lockMode = null, $lockVersion = null)
 * @method Evenement|null findOneBy(array $criteria, array $orderBy = null)
 * @method Evenement[]    findAll()
 * @method Evenement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EvenementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, evenement::class);
    }

    // Function to find events by user ID
    public function findByUserId($userId)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.id_user = :userId')
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getResult();
    }

    // Function to count events by date debut (dhe)
    public function countByDateDebut(): array
    {
    $connection = $this->getEntityManager()->getConnection();
    $sql = '
        SELECT DATE(date_DHE) as date, COUNT(id_E) as count
        FROM evenement
        GROUP BY DATE(date_DHE)
    ';
    $statement = $connection->executeQuery($sql);

    return $statement->fetchAllAssociative();
    }

    // Function to count events by categorie
    public function countByCategorie()
    {
        return $this->createQueryBuilder('e')
            ->select('COUNT(e.id_E) as eventCount', 'e.categorie_E')
            ->groupBy('e.categorie_E')
            ->getQuery()
            ->getResult();
    }

    public function countByMonth(): array
    {
        $connection = $this->getEntityManager()->getConnection();
        $sql = '
            SELECT MONTH(date_DHE) as month, COUNT(id_E) as count
            FROM evenement
            GROUP BY MONTH(date_DHE)
        ';
        $statement = $connection->executeQuery($sql);

        return $statement->fetchAllAssociative();
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(evenement $entity, bool $flush = true): void
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
    public function remove(evenement $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }
    

public function findByNomE(string $nom): array
{
    return $this->createQueryBuilder('e')
        ->andWhere('e.nomE LIKE :nom')
        ->setParameter('nom', '%' . $nom . '%')
        ->getQuery()
        ->getResult();
}


    // /**
    //  * @return Evenement[] Returns an array of Evenement objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Evenement
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
