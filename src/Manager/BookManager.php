<?php

declare(strict_types=1);

namespace App\Manager;

use App\Entity\Book;
use App\Exception\AppException;
use App\Model\PaginatedDataModel;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;

final class BookManager
{
    /**
     * @param EntityManagerInterface $entityManager
     * @param AuthorManager $authorManager
     * @param BookRepository $bookRepo
     */
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly AuthorManager $authorManager,
        private readonly BookRepository $bookRepo
    ) {}

    /**
     * @param string $id
     * @return Book
     * @throws AppException
     */
    public function get(string $id): Book
    {
        $book = $this->bookRepo->find($id);
        if (!$book instanceof Book) {
            throw new AppException('error.book_not_found', Response::HTTP_NOT_FOUND);
        }

        return $book;
    }

    /**
     * @param array $filters
     * @return PaginatedDataModel
     * @throws AppException
     */
    public function findBy(array $filters): PaginatedDataModel
    {
        try {
            $page = $filters['page'] ?? 1;
            $limit = $filters['limit'] ?? 10;

            $total = $this->bookRepo->countByFilter($filters);
            $items = $this->bookRepo->findByFilter($filters, (int) $page, (int) $limit);

            return new PaginatedDataModel($total, (int) $limit, (int) $page, $items);
        } catch (\Throwable $e) {
            throw new AppException($e->getMessage());
        }
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
        $author = $this->authorManager->get($authorId);
        try {
            $book = new Book($author, $name, $price);
            $this->entityManager->persist($book);
            $this->entityManager->flush();
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
        $author = $this->authorManager->get($authorId);
        $book = $this->get($id);
        try {
            $book->setAuthor($author);
            $book->setName($name);
            $book->setPrice($price);
            $this->entityManager->flush();
        } catch (\Throwable $e) {
            throw new AppException($e->getMessage(), $e->getCode());
        }

        return $book;
    }

    /**
     * @param string $id
     * @throws AppException
     */
    public function delete(string $id): void
    {
        $book = $this->get($id);
        try {
            $this->entityManager->remove($book);
            $this->entityManager->flush();
        } catch (\Throwable $e) {
            throw new AppException($e->getMessage(), $e->getCode());
        }
    }
}
