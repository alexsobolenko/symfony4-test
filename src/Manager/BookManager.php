<?php

declare(strict_types=1);

namespace App\Manager;

use App\Entity\Author;
use App\Entity\Book;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class BookManager
 * @package App\Manager
 */
class BookManager
{
    /** @var EntityManagerInterface */
    protected $em;

    /** @var BookRepository */
    protected $repo;

    /**
     * BookManager constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->repo = $em->getRepository(Book::class);
    }

    /**
     * @param string $id
     * @return Book
     * @throws Exception
     */
    public function getById(string $id): Book
    {
        $book = $this->repo->find($id);

        if (!$book instanceof Book) {
            throw new Exception("Book not found", Response::HTTP_NOT_FOUND);
        }

        return $book;
    }

    /**
     * @return array
     */
    public function findAll(): array
    {
        return $this->repo->findBy([], ["author" => "ASC", "name" => "ASC"]);
    }

    /**
     * @param string $name
     * @param Author $author
     * @param float $price
     * @return Book
     */
    public function create(string $name, Author $author, float $price): Book
    {
        $book = new Book();
        $book->setName($name);
        $book->setAuthor($author);
        $book->setPrice($price);
        $this->em->persist($book);
        $this->em->flush();

        return $book;
    }

    /**
     * @param string $id
     * @param string $name
     * @param Author $author
     * @param float $price
     * @return Book
     * @throws Exception
     */
    public function edit(string $id, string $name, Author $author, float $price): Book
    {
        $book = $this->getById($id);
        $book->setName($name);
        $book->setAuthor($author);
        $book->setPrice($price);
        $this->em->persist($book);
        $this->em->flush();

        return $book;
    }

    /**
     * @param string $id
     * @throws Exception
     */
    public function delete(string $id): void
    {
        $book = $this->getById($id);
        $this->em->remove($book);
        $this->em->flush();
    }
}
