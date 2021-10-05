<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Author;
use App\Entity\Book;
use App\Model\AuthorModel;
use App\Model\BookModel;
use App\Form\AuthorType;
use App\Form\BookType;
use App\Repository\AuthorRepository;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;

class AppController extends AbstractController
{
    /**
     * @Route(path="", methods={"GET"}, name="app_index")
     * @return Response
     */
    public function indexAction(): Response
    {
        return $this->redirectToRoute('app_authors_list');
    }

    /**
     * @Route(path="/list/authors", methods={"GET"}, name="app_authors_list")
     * @return Response
     */
    public function authorsListAction(): Response
    {
        $authors = $this->getAuthorRepository()->findBy([], ['name' => 'ASC']);

        return $this->render('author/list.html.twig', [
            'title' => 'Authors',
            'authors' => $authors,
        ]);
    }

    /**
     * @Route(path="/create/author", methods={"GET","POST"}, name="app_author_create")
     * @Route(path="/edit/author/{id}", methods={"GET","POST"}, name="app_author_edit")
     * @param Request $request
     * @param string|null $id
     * @return Response
     */
    public function authorDetailsAction(Request $request, ?string $id = null): Response
    {
        if ($id === null) {
            $model = new AuthorModel();
            $title = 'Add new author';
        } else {
            $repo = $this->getAuthorRepository();
            $author = $repo->find($id);
            $model = AuthorModel::map($author);
            $title = 'Edit ' . $author->getName();
        }

        $form = $this->createForm(AuthorType::class, $model);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $model = $form->getData();
            $em = $this->getEntityManager();
            if ($id === null) {
                $author = new Author($model->name);
                $em->persist($author);
            } else {
                $author->setName($model->name);
            }

            $em->flush();
            $this->addFlash('success', 'Author created');

            return $this->redirectToRoute('app_authors_list');
        }

        return $this->render('author/form.html.twig', [
            'title' => $title,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route(path="/delete/author/{id}", methods={"GET","POST"}, name="app_author_delete")
     * @param string $id
     * @return Response
     */
    public function authorDeleteAction(string $id): Response
    {
        $em = $this->getEntityManager();
        $author = $this->getAuthorRepository()->find($id);
        $em->remove($author);
        $em->flush();
        $this->addFlash('success', 'Author deleted');

        return $this->redirectToRoute('app_authors_list');
    }

    /**
     * @Route(path="/list/books", methods={"GET"}, name="app_books_list")
     * @return Response
     */
    public function booksListAction(): Response
    {
        $books = $this->getBookRepository()->findBy([], ['price' => 'ASC']);

        return $this->render('book/list.html.twig', [
            'title' => 'Books',
            'books' => $books,
        ]);
    }

    /**
     * @Route(path="/create/book", methods={"GET","POST"}, name="app_book_create")
     * @Route(path="/edit/book/{id}", methods={"GET","POST"}, name="app_book_edit")
     * @param Request $request
     * @param string|null $id
     * @return Response
     */
    public function bookEditAction(Request $request, ?string $id = null): Response
    {
        if ($id === null) {
            if (!$this->getAuthorRepository()->hasAuthors()) {
                $this->addFlash('danger', 'Has no authors yet. Create one new first');
            }

            $model = new BookModel();
            $title = 'Add new book';
        } else {
            $repo = $this->getBookRepository();
            $book = $repo->find($id);
            $model = BookModel::map($book);
            $title = 'Edit ' . $book->getName() . ' (' . $book->getAuthor()->getName() . ')';
        }

        $form = $this->createForm(BookType::class, $model, [
            'em' => $this->getEntityManager(),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $model = $form->getData();
            $em = $this->getEntityManager();
            $author = $this->getAuthorRepository()->find($model->author);

            if ($id === null) {
                $book = new Book($author, $model->name, $model->price);
                $em->persist($book);
            } else {
                $book->setAuthor($author);
                $book->setName($model->name);
                $book->setPrice($model->price);
            }

            $em->flush();
            $this->addFlash('success', 'Book created');

            return $this->redirectToRoute('app_books_list');
        }

        return $this->render('book/form.html.twig', [
            'title' => $title,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route(path="/delete/book/{id}", methods={"GET","POST"}, name="app_book_delete")
     * @param string $id
     * @return Response
     */
    public function bookDeleteAction(string $id): Response
    {
        $em = $this->getEntityManager();
        $book = $this->getBookRepository()->find($id);
        $em->remove($book);
        $em->flush();
        $this->addFlash('success', 'Book deleted');

        return $this->redirectToRoute('app_books_list');
    }

    /**
     * @return EntityManagerInterface
     */
    private function getEntityManager(): EntityManagerInterface
    {
        /** @var EntityManagerInterface $em */
        $em = $this->getDoctrine()->getManager();

        return $em;
    }

    /**
     * @return AuthorRepository
     */
    private function getAuthorRepository(): AuthorRepository
    {
        /** @var AuthorRepository $repo */
        $repo = $this->getDoctrine()->getRepository(Author::class);

        return $repo;
    }

    /**
     * @return BookRepository
     */
    private function getBookRepository(): BookRepository
    {
        /** @var BookRepository $repo */
        $repo = $this->getDoctrine()->getRepository(Book::class);

        return $repo;
    }
}
