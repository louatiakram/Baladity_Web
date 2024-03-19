<?php

namespace App\Repository;

use App\Entity\tache;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<tache>
 *
 * @method tache|null find($id, $lockMode = null, $lockVersion = null)
 * @method tache|null findOneBy(array $criteria, array $orderBy = null)
 * @method tache[]    findAll()
 * @method tache[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TacheRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, tache::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(tache $entity, bool $flush = true): void
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
    public function remove(tache $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }


    // Define findByQuery method to search for tasks based on a query

    // /**
    //  * @return tache[] Returns an array of tache objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?tache
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    public function findByTitre(string $query): array
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.titre_T LIKE :query')
            ->setParameter('query', '%' . $query . '%')
            ->orderBy('t.id_T', 'ASC') // Assuming 'idT' is the primary key field
            ->getQuery()
            ->getResult();
    }
}
