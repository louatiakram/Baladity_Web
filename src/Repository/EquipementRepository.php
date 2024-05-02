<?php

namespace App\Repository;

use App\Entity\Equipement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @extends ServiceEntityRepository<Equipement>
 *
 * @method Equipement|null find($id, $lockMode = null, $lockVersion = null)
 * @method Equipement|null findOneBy(array $criteria, array $orderBy = null)
 * @method Equipement[]    findAll()
 * @method Equipement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EquipementRepository extends ServiceEntityRepository
{
    private EntityManagerInterface $entityManager;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, Equipement::class);
        $this->entityManager = $entityManager;
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Equipement $entity, bool $flush = true): void
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
    public function remove(Equipement $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // /**
    //  * @return Equipement[] Returns an array of Equipement objects
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
    public function findOneBySomeField($value): ?Equipement
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    // Méthode pour récupérer tous les équipements paginés
    public function findAllPaginated(int $limit, int $offset): array
    {
        return $this->createQueryBuilder('e')
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->getQuery()
            ->getResult();
    }

    // Méthode pour compter tous les équipements
    public function countAll(): int
    {
        return $this->createQueryBuilder('e')
            ->select('COUNT(e)') // Utiliser COUNT(e) au lieu de COUNT(e.id)
            ->getQuery()
            ->getSingleScalarResult();
    }
    public function findBySearchAndCategory(?string $query, ?string $category, int $limit, int $offset): array
{
    $qb = $this->createQueryBuilder('e');

    // Si une recherche est effectuée, ajoutez une clause WHERE pour filtrer par nom d'équipement
    if ($query) {
        $qb->andWhere($qb->expr()->like('e.nom_eq', ':query'))
           ->setParameter('query', '%'.$query.'%');
    }

    // Si une catégorie est sélectionnée, ajoutez une clause WHERE pour filtrer par catégorie
    if ($category) {
        $qb->andWhere('e.categorie_eq = :category')
           ->setParameter('category', $category);
    }

    // Limitez le nombre de résultats et définissez l'offset
    $qb->setMaxResults($limit)
       ->setFirstResult($offset);

    return $qb->getQuery()->getResult();
}
public function findAllCategories(): array
    {
        $qb = $this->createQueryBuilder('e')
            ->select('DISTINCT e.categorie_eq');

        $result = $qb->getQuery()->getResult();

        return array_column($result, 'categorie_eq');
    }
    public function countEquipementsAddedByDay(): array
    {
        $connection = $this->entityManager->getConnection();
        $sql = '
        SELECT DATE(date_ajouteq) AS date, SUM(quantite_eq) AS count
        FROM equipement
        GROUP BY DATE(date_ajouteq)
        ';
        $statement = $connection->executeQuery($sql);

        return $statement->fetchAllAssociative();
    }

    public function countEquipementsAddedByMonth(): array
    {
        $connection = $this->entityManager->getConnection();
        $sql = '
        SELECT MONTH(date_ajouteq) as month, SUM(quantite_eq) as count
        FROM equipement
        GROUP BY MONTH(date_ajouteq)
        ';
        $statement = $connection->executeQuery($sql);

        return $statement->fetchAllAssociative();
    }
    public function countEquipementsByCategory(string $category): int
{
    return $this->createQueryBuilder('e')
        ->select('SUM(e.quantite_eq)')
        ->where('e.categorie_eq = :category')
        ->setParameter('category', $category)
        ->getQuery()
        ->getSingleScalarResult();
}
}
