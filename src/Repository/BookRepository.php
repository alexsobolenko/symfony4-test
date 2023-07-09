<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Author;
use App\Entity\Book;
use App\Exception\AppException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;

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
     * @param string $id
     * @return Book
     * @throws AppException
     */
    public function get(string $id): Book
    {
        $book = $this->find($id);
        if (!$book instanceof Book) {
            throw new AppException('error.book_not_found', Response::HTTP_NOT_FOUND);
        }

        return $book;
    }

    /**
     * @param string $authorId
     * @param string $name
     * @param float $price
     * @return Book
     * @throws AppException
     */
    public function create(string $authorId, string $name, float $price): Book
    {
        $author = $this->_em->getRepository(Author::class)->get($authorId);
        try {
            $book = new Book($author, $name, $price);
            $this->_em->persist($book);
            $this->_em->flush();
        } catch (\Throwable $e) {
            throw new AppException($e->getMessage(), $e->getCode());
        }

        return $book;
    }

    /**
     * @param string $id
     * @param string $authorId
     * @param string $name
     * @param float $price
     * @return Book
     * @throws AppException
     */
    public function edit(string $id, string $authorId, string $name, float $price): Book
    {
        $author = $this->_em->getRepository(Author::class)->get($authorId);
        $book = $this->get($id);
        try {
            $book->setAuthor($author);
            $book->setName($name);
            $book->setPrice($price);
            $this->_em->flush();
        } catch (\Throwable $e) {
            throw new AppException($e->getMessage(), $e->getCode());
        }

        return $book;
    }

    /**
     * @param string $id
     * @return Book
     * @throws AppException
     */
    public function delete(string $id): Book
    {
        $book = $this->get($id);
        try {
            $this->_em->remove($book);
            $this->_em->flush();
        } catch (\Throwable $e) {
            throw new AppException($e->getMessage(), $e->getCode());
        }

        return $book;
    }

    /**
     * @param array $filters
     * @return int
     * @throws AppException
     */
    public function countByFilter(array $filters): int
    {
        $qb = $this->createQuery($filters);
        $qb->select('COUNT(b.id)');

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

        $qb->addOrderBy('b.price', 'ASC');
        $qb->addOrderBy('b.name', 'ASC');

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
        $qb = $this->createQueryBuilder('b');

        $name = $filters['name'] ?? null;
        if ($name) {
            $qb->andWhere($qb->expr()->like('b.name', ':name'));
            $qb->setParameter('name', '%' . $name . '%');
        }

        $author = $filters['author'] ?? null;
        if ($author) {
            $qb->leftJoin('b.author', 'a');
            $qb->andWhere($qb->expr()->like('a.name', ':author'));
            $qb->setParameter('author', '%' . $author . '%');
        }

        return $qb;
    }
}
