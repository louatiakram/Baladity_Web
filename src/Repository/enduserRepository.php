<?php

namespace App\Repository;

use App\Entity\enduser;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<Enduser>
 *
 * @method Enduser|null find($id, $lockMode = null, $lockVersion = null)
 * @method Enduser|null findOneBy(array $criteria, array $orderBy = null)
 * @method Enduser[]    findAll()
 * @method Enduser[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class enduserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
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

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof enduser) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newHashedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    // /**
    //  * @return Enduser[] Returns an array of Enduser objects
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
    public function findOneBySomeField($value): ?Enduser
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function findByTypeUser(string $type): array
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.type_user = :type')
            ->setParameter('type', $type)
            ->getQuery()
            ->getResult();
    }

    public function countUsersPerMuni(): array
{
    return $this->createQueryBuilder('u')
        ->select('COUNT(u.id_user) as userCount', 'm.nom_muni as nom_muni')
        ->leftJoin('u.id_muni', 'm')
        ->groupBy('u.id_muni')
        ->getQuery()
        ->getResult();
}
public function countUsersPerType(): array
{
    return $this->createQueryBuilder('u')
        ->select('COUNT(u.id_user) as userCount', 'u.type_user')
        ->where('u.type_user != :type')
        ->setParameter('type', 'admin')
        ->groupBy('u.type_user')
        ->getQuery()
        ->getResult();
}

}
