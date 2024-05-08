<?php

namespace App\Repository;

use App\Entity\actualite;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Actualite>
 *
 * @method Actualite|null find($id, $lockMode = null, $lockVersion = null)
 * @method Actualite|null findOneBy(array $criteria, array $orderBy = null)
 * @method Actualite[]    findAll()
 * @method Actualite[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ActualiteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Actualite::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Actualite $entity, bool $flush = true): void
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
    public function remove(Actualite $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // /**
    //  * @return Actualite[] Returns an array of Actualite objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Actualite
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    public function findByTitreA($titreA)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.titre_a LIKE :titreA')
            ->setParameter('titreA', '%' . $titreA . '%')
            ->orderBy('a.date_a', 'DESC')
            ->getQuery()
            ->getResult();
    }

    // Define a custom method to fetch actualités based on the titre_a field
    public function findByTitre($titre)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.titre_a = :titre')
            ->setParameter('titre', $titre)
            ->getQuery()
            ->getResult();
    }
    public function countByDate(): array
    {
    $connection = $this->getEntityManager()->getConnection();
    $sql = '
        SELECT DATE(date_a) as date, COUNT(id_a) as count
        FROM actualite
        GROUP BY DATE(date_a)
    ';
    $statement = $connection->executeQuery($sql);

    return $statement->fetchAllAssociative();
    }
public function countByMonth(): array
    {
        $connection = $this->getEntityManager()->getConnection();
        $sql = '
            SELECT MONTH(date_a) as month, COUNT(id_a) as count
            FROM actualite
            GROUP BY MONTH(date_a)
        ';
        $statement = $connection->executeQuery($sql);

        return $statement->fetchAllAssociative();
    }
}
