<?php

namespace App\Repository;

use App\Entity\publicite;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Publicite>
 *
 * @method Publicite|null find($id, $lockMode = null, $lockVersion = null)
 * @method Publicite|null findOneBy(array $criteria, array $orderBy = null)
 * @method Publicite[]    findAll()
 * @method Publicite[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PubliciteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Publicite::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Publicite $entity, bool $flush = true): void
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
    public function remove(Publicite $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }
    public function findByTitrePub(string $titre): array
    {
        return $this->createQueryBuilder('p')
            ->where('p.titre_pub LIKE :titre') // recherche avec LIKE pour permettre la recherche partielle
            ->setParameter('titre', '%' . $titre . '%') // ajout des wildcards pour recherche flexible
            ->orderBy('p.titre_pub', 'ASC') // ordre par titre (optionnel)
            ->getQuery()
            ->getResult();
    }
    public function findSortedPublicites(string $sortBy = 'id', string $sortOrder = 'ASC'): array
    {
        // Add debugging information to check parameter values
        dump("Sorting by: $sortBy, Order: $sortOrder");
    
        $allowedSortFields = ['id', 'titre_pub', 'date_pub']; // Authorized fields
        if (!in_array($sortBy, $allowedSortFields)) {
            $sortBy = 'id'; // Default field if invalid
        }
    
        $allowedSortOrders = ['ASC', 'DESC']; // Authorized orders
        if (!in_array($sortOrder, $allowedSortOrders)) {
            $sortOrder = 'ASC'; // Default order if invalid
        }
    
        return $this->createQueryBuilder('p')
            ->orderBy("p.$sortBy", $sortOrder)
            ->getQuery()
            ->getResult();
    }
    public function countByOffer(): array
    {
        return $this->createQueryBuilder('p')
            ->select('p.offre_pub as offre, COUNT(p.id_pub) as count')
            ->groupBy('p.offre_pub')
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
    
    // /**
    //  * @return Publicite[] Returns an array of Publicite objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Publicite
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
