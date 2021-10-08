<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Book;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Book|null find($id, $lockMode = null, $lockVersion = null)
 * @method Book|null findOneBy(array $criteria, array $orderBy = null)
 * @method Book[]    findAll()
 * @method Book[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Book::class);
    }

    /**
     * @param array $filters
     * @return int
     */
    public function countByFilter(array $filters): int
    {
        $qb = $this->createQuery($filters);
        $qb->select('COUNT(b.id)');

        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * @param array $filters
     * @param int $page
     * @param int $limit
     * @return array
     */
    public function findByFilter(array $filters, int $page, int $limit): array
    {
        $offset = ($page - 1) * $limit;
        $qb = $this->createQuery($filters);
        $qb->setMaxResults($limit);
        $qb->setFirstResult($offset);

        $qb->addOrderBy('b.price', 'ASC');
        $qb->addOrderBy('b.name', 'ASC');

        return $qb->getQuery()->getResult();
    }

    /**
     * @param array $filters
     * @return QueryBuilder
     */
    private function createQuery(array $filters): QueryBuilder
    {
        $name = $filters['name'] ?? null;
        $author = $filters['author'] ?? null;

        $qb = $this->createQueryBuilder('b');

        if ($name) {
            $qb->andWhere($qb->expr()->like('b.name', ':name'));
            $qb->setParameter('name', '%' . $name . '%');
        }

        if ($author) {
            $qb->leftJoin('b.author', 'a');
            $qb->andWhere($qb->expr()->like('a.name', ':author'));
            $qb->setParameter('author', '%' . $author . '%');
        }

        return $qb;
    }
}
