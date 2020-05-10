<?php

declare(strict_types=1);

namespace App\Manager;

use App\Entity\Author;
use App\Repository\AuthorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class AuthorManager
 * @package App\Manager
 */
class AuthorManager
{
    /** @var EntityManagerInterface */
    protected $em;

    /** @var AuthorRepository */
    protected $repo;

    /**
     * AuthorManager constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->repo = $em->getRepository(Author::class);
    }

    /**
     * @param string $id
     * @return Author
     * @throws Exception
     */
    public function getById(string $id): Author
    {
        $author = $this->repo->find($id);

        if (!$author instanceof Author) {
            throw new Exception("Author not found", Response::HTTP_NOT_FOUND);
        }

        return $author;
    }

    /**
     * @return array
     */
    public function findAll(): array
    {
        return $this->repo->findBy([], ["name" => "ASC"]);
    }

    /**
     * @param string $name
     * @return Author
     */
    public function create(string $name): Author
    {
        $author = new Author();
        $author->setName($name);
        $this->em->persist($author);
        $this->em->flush();

        return $author;
    }

    /**
     * @param string $id
     * @param string $name
     * @return Author
     * @throws Exception
     */
    public function edit(string $id, string $name): Author
    {
        $author = $this->getById($id);
        $author->setName($name);
        $this->em->persist($author);
        $this->em->flush();

        return $author;
    }

    /**
     * @param string $id
     * @throws Exception
     */
    public function delete(string $id): void
    {
        $author = $this->getById($id);

        if ($author->getBooks()->count() > 0) {
            throw new Exception("Author can't to be deleted while related books are exist", Response::HTTP_BAD_REQUEST);
        }

        $this->em->remove($author);
        $this->em->flush();
    }
}
