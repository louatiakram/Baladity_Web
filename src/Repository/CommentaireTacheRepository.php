<?php

namespace App\Repository;

use App\Entity\commentairetache;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<commentairetache>
 *
 * @method commentairetache|null find($id, $lockMode = null, $lockVersion = null)
 * @method commentairetache|null findOneBy(array $criteria, array $orderBy = null)
 * @method commentairetache[]    findAll()
 * @method commentairetache[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentaireTacheRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, commentairetache::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(commentairetache $entity, bool $flush = true): void
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
    public function remove(commentairetache $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // /**
    //  * @return commentairetache[] Returns an array of commentairetache objects
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

    /*
    public function findOneBySomeField($value): ?commentairetache
    {
    return $this->createQueryBuilder('c')
    ->andWhere('c.exampleField = :val')
    ->setParameter('val', $value)
    ->getQuery()
    ->getOneOrNullResult()
    ;
    }
     */
    public function findByCommentaire(string $query): array
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.texte_C LIKE :query')
            ->setParameter('query', '%' . $query . '%')
            ->orderBy('t.id_C', 'ASC') // Assuming 'idT' is the primary key field
            ->getQuery()
            ->getResult();
    }
}
