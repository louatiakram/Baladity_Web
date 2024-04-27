<?php

namespace App\Repository;

use App\Entity\tache;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\QueryBuilder;
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

    public function findByUserType(string $typeUser): array
    {
        $qb = $this->createQueryBuilder('t')
            ->select('t')
            ->innerJoin('t.id_user', 'u') // Changed leftJoin to innerJoin
            ->where('u.type_user = :typeUser')
            ->setParameter('typeUser', $typeUser)
            ->getQuery();

        return $qb->getResult();
    }

    public function findByNom($value): QueryBuilder
    {
        $qb = $this->createQueryBuilder('q');
        $qb->andWhere('q.titre_T LIKE :val')
            ->setParameter('val', '%' . $value . '%');
        return $qb;
    }

    public function getUsersTasksCount(): array
    {
        $qb = $this->createQueryBuilder('t')
            ->select('COUNT(t.id_T) as task_count', 'u.nom_user as user_name')
            ->leftJoin('t.id_user', 'u')
            ->where('t.etat_T = :etat')
            ->setParameter('etat', 'DONE') // Filter tasks with etat_T = 'DONE'
            ->groupBy('t.id_user')
            ->orderBy('task_count', 'DESC')
            ->setMaxResults(8) // Limit to top 10 users
            ->getQuery();

        return $qb->getResult();
    }

    public function countByEtat(string $etat): int
    {
        $qb = $this->createQueryBuilder('t')
            ->select('COUNT(t.id_T)')
            ->where('t.etat_T = :etat')
            ->setParameter('etat', $etat);

        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    public function findByDateRange(\DateTimeInterface $startDate, \DateTimeInterface $endDate): QueryBuilder
    {
        $qb = $this->createQueryBuilder('t')
            ->where('t.date_FT >= :startDate')
            ->andWhere('t.date_FT <= :endDate')
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate);

        return $qb;
    }

}