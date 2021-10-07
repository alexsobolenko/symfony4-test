<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Author;
use App\Entity\Book;
use App\Repository\AuthorRepository;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

abstract class BaseController extends AbstractController
{
    /**
     * @return EntityManagerInterface
     */
    protected function getEntityManager(): EntityManagerInterface
    {
        /** @var EntityManagerInterface $em */
        $em = $this->getDoctrine()->getManager();

        return $em;
    }

    /**
     * @return AuthorRepository
     */
    protected function getAuthorRepository(): AuthorRepository
    {
        /** @var AuthorRepository $repo */
        $repo = $this->getDoctrine()->getRepository(Author::class);

        return $repo;
    }

    /**
     * @return BookRepository
     */
    protected function getBookRepository(): BookRepository
    {
        /** @var BookRepository $repo */
        $repo = $this->getDoctrine()->getRepository(Book::class);

        return $repo;
    }
}
