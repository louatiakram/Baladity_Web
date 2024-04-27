<?php

namespace App\Repository;

use App\Entity\enduser;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<enduser>
 *
 * @method enduser|null find($id, $lockMode = null, $lockVersion = null)
 * @method enduser|null findOneBy(array $criteria, array $orderBy = null)
 * @method enduser[]    findAll()
 * @method enduser[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EndUserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, enduser::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(enduser $entity, bool $flush = true): void
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
    public function remove(enduser $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // /**
    //  * @return enduser[] Returns an array of enduser objects
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
public function findOneBySomeField($value): ?enduser
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
