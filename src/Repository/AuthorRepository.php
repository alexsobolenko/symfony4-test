<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Author;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Author|null find($id, $lockMode = null, $lockVersion = null)
 * @method Author|null findOneBy(array $criteria, array $orderBy = null)
 * @method Author[]    findAll()
 * @method Author[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AuthorRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Author::class);
    }

    /**
     * @return bool
     */
    public function hasAuthors(): bool
    {
        $authors = $this->findAll();

        return count($authors) > 0;
    }

    /**
     * @param array $filters
     * @return int
     */
    public function countByFilter(array $filters): int
    {
        $qb = $this->createQuery($filters);
        $qb->select('COUNT(a.id)');

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

        $qb->addOrderBy('a.name', 'ASC');

        return $qb->getQuery()->getResult();
    }

    /**
     * @param array $filters
     * @return QueryBuilder
     */
    private function createQuery(array $filters): QueryBuilder
    {
        $name = $filters['name'] ?? null;

        $qb = $this->createQueryBuilder('a');

        if ($name) {
            $qb->andWhere($qb->expr()->like('a.name', ':name'));
            $qb->setParameter('name', '%' . $name . '%');
        }

        return $qb;
    }
}
