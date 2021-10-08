<?php

declare(strict_types=1);

namespace App\Manager;

use App\Entity\Book;
use App\Model\PaginatedDataModel;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class BookManager
{
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    /**
     * @var AuthorManager
     */
    private AuthorManager $authorManager;

    /**
     * @var BookRepository
     */
    private BookRepository $bookRepo;

    /**
     * @param EntityManagerInterface $entityManager
     * @param AuthorManager $authorManager
     * @param BookRepository $bookRepo
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        AuthorManager $authorManager,
        BookRepository $bookRepo
    ) {
        $this->entityManager = $entityManager;
        $this->authorManager = $authorManager;
        $this->bookRepo = $bookRepo;
    }

    /**
     * @param string $id
     * @return Book
     * @throws NotFoundHttpException
     */
    public function get(string $id): Book
    {
        $book = $this->bookRepo->find($id);
        if (!$book instanceof Book) {
            throw new NotFoundHttpException('Book not found by id');
        }

        return $book;
    }

    /**
     * @param array $filters
     * @return PaginatedDataModel
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
            throw new BadRequestException($e->getMessage());
        }
    }

    /**
     * @param string $authorId
     * @param string $name
     * @param float $price
     * @return Book
     * @throws NotFoundHttpException
     */
    public function create(string $authorId, string $name, float $price): Book
    {
        $author = $this->authorManager->get($authorId);

        $book = new Book($author, $name, $price);
        $this->entityManager->persist($book);
        $this->entityManager->flush();

        return $book;
    }

    /**
     * @param string $id
     * @param string $authorId
     * @param string $name
     * @param float $price
     * @return Book
     * @throws NotFoundHttpException
     */
    public function edit(string $id, string $authorId, string $name, float $price): Book
    {
        $author = $this->authorManager->get($authorId);

        $book = $this->get($id);
        $book->setAuthor($author);
        $book->setName($name);
        $book->setPrice($price);
        $this->entityManager->flush();

        return $book;
    }

    /**
     * @param string $id
     * @throws NotFoundHttpException
     */
    public function delete(string $id): void
    {
        $book = $this->get($id);
        $this->entityManager->remove($book);
        $this->entityManager->flush();
    }
}
