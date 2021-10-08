<?php

declare(strict_types=1);

namespace App\Manager;

use App\Entity\Author;
use App\Exception\AppException;
use App\Model\PaginatedDataModel;
use App\Repository\AuthorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;

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
     * @throws AppException
     */
    public function get(string $id): Author
    {
        $author = $this->authorRepo->find($id);
        if (!$author instanceof Author) {
            throw new AppException('error.author_not_found', Response::HTTP_NOT_FOUND);
        }

        return $author;
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

            $total = $this->authorRepo->countByFilter($filters);
            $items = $this->authorRepo->findByFilter($filters, (int) $page, (int) $limit);

            return new PaginatedDataModel($total, (int) $limit, (int) $page, $items);
        } catch (\Throwable $e) {
            throw new AppException($e->getMessage());
        }
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
            $this->entityManager->persist($author);
            $this->entityManager->flush();
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
            $this->entityManager->flush();
        } catch (\Throwable $e) {
            throw new AppException($e->getMessage(), $e->getCode());
        }

        return $author;
    }

    /**
     * @param string $id
     * @throws AppException
     */
    public function delete(string $id): void
    {
        $author = $this->get($id);
        try {
            $this->entityManager->remove($author);
            $this->entityManager->flush();
        } catch (\Throwable $e) {
            throw new AppException($e->getMessage(), $e->getCode());
        }

    }
}
