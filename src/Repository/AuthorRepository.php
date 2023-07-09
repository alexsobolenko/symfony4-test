<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Author;
use App\Exception\AppException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

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
     * @throws AppException
     */
    public function countByFilter(array $filters): int
    {
        $qb = $this->createQuery($filters);
        $qb->select('COUNT(a.id)');

        try {
            return (int) $qb->getQuery()->getSingleScalarResult();
        } catch (ORMException $e) {
            throw new AppException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param array $filters
     * @param int $page
     * @param int $limit
     * @return array
     * @throws AppException
     */
    public function findByFilter(array $filters, int $page, int $limit): array
    {
        $offset = ($page - 1) * $limit;
        $qb = $this->createQuery($filters);
        $qb->setMaxResults($limit);
        $qb->setFirstResult($offset);

        $qb->addOrderBy('a.name', 'ASC');

        try {
            return $qb->getQuery()->getResult();
        } catch (ORMException $e) {
            throw new AppException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param array $filters
     * @return QueryBuilder
     */
    private function createQuery(array $filters): QueryBuilder
    {
        $qb = $this->createQueryBuilder('a');
        $name = $filters['name'] ?? null;
        if ($name) {
            $qb->andWhere($qb->expr()->like('a.name', ':name'));
            $qb->setParameter('name', '%' . $name . '%');
        }

        return $qb;
    }
}
