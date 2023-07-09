<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Author;
use App\Exception\AppException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;

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
     * @param string $id
     * @return Author
     * @throws AppException
     */
    public function get(string $id): Author
    {
        $author = $this->find($id);
        if (!$author instanceof Author) {
            throw new AppException('error.author_not_found', Response::HTTP_NOT_FOUND);
        }

        return $author;
    }

    /**
     * @param string $name
     * @return Author
     * @throws AppException
     */
    public function create(string $name): Author
    {
        try {
            $author = new Author($name);
            $this->_em->persist($author);
            $this->_em->flush();
        } catch (\Throwable $e) {
            throw new AppException($e->getMessage(), $e->getCode());
        }

        return $author;
    }

    /**
     * @param string $id
     * @param string $name
     * @return Author
     * @throws AppException
     */
    public function edit(string $id, string $name): Author
    {
        $author = $this->get($id);
        try {
            $author->setName($name);
            $this->_em->flush();
        } catch (\Throwable $e) {
            throw new AppException($e->getMessage(), $e->getCode());
        }

        return $author;
    }

    /**
     * @param string $id
     * @return Author
     * @throws AppException
     */
    public function delete(string $id): Author
    {
        $author = $this->get($id);
        try {
            $this->_em->remove($author);
            $this->_em->flush();
        } catch (\Throwable $e) {
            throw new AppException($e->getMessage(), $e->getCode());
        }

        return $author;
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
     * @return array
     * @throws AppException
     */
    public function findByFilter(array $filters): array
    {
        $page = $filters['page'] ?? 1;
        $limit = $filters['limit'] ?? 1;

        $qb = $this->createQuery($filters);
        $qb->setMaxResults($limit);
        $qb->setFirstResult(($page - 1) * $limit);

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
