<?php

declare(strict_types=1);

namespace App\Manager;

use App\Entity\Author;
use App\Repository\AuthorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AuthorManager
{
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    /**
     * @var AuthorRepository
     */
    private AuthorRepository $authorRepo;

    /**
     * @param EntityManagerInterface $entityManager
     * @param AuthorRepository $authorRepo
     */
    public function __construct(EntityManagerInterface $entityManager, AuthorRepository $authorRepo)
    {
        $this->entityManager = $entityManager;
        $this->authorRepo = $authorRepo;
    }

    /**
     * @param string $id
     * @return Author
     * @throws NotFoundHttpException
     */
    public function get(string $id): Author
    {
        $author = $this->authorRepo->find($id);
        if (!$author instanceof Author) {
            throw new NotFoundHttpException('Author not found by id');
        }

        return $author;
    }

    /**
     * @return Author[]
     */
    public function findAll(): array
    {
        return $this->authorRepo->findBy([], ['name' => 'ASC']);
    }

    /**
     * @param string $name
     * @return Author
     */
    public function create(string $name): Author
    {
        $author = new Author($name);
        $this->entityManager->persist($author);
        $this->entityManager->flush();

        return $author;
    }

    /**
     * @param string $id
     * @param string $name
     * @return Author
     * @throws NotFoundHttpException
     */
    public function edit(string $id, string $name): Author
    {
        $author = $this->get($id);
        $author->setName($name);
        $this->entityManager->flush();

        return $author;
    }

    /**
     * @param string $id
     * @throws NotFoundHttpException
     */
    public function delete(string $id): void
    {
        $author = $this->get($id);
        $this->entityManager->remove($author);
        $this->entityManager->flush();
    }
}
