<?php

declare(strict_types=1);

namespace App\Controller;

use App\DataProvider\MainDataProvider;
use App\Entity\Author;
use App\Entity\Book;
use App\Form\AuthorType;
use App\Form\BookType;
use App\Manager\AuthorManager;
use App\Manager\BookManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Exception;
use Symfony\Component\Routing\Annotation\Route;

class AppController extends AbstractController
{
    /** @var AuthorManager */
    protected $authorManager;

    /** @var BookManager */
    protected $bookManager;

    /** @var RequestStack */
    protected $requestStack;

    /**
     * AppController constructor.
     * @param AuthorManager $authorManager
     * @param BookManager $bookManager
     * @param RequestStack $requestStack
     */
    public function __construct(
        AuthorManager $authorManager,
        BookManager $bookManager,
        RequestStack $requestStack
    ) {
        $this->authorManager = $authorManager;
        $this->bookManager = $bookManager;
        $this->requestStack = $requestStack;
    }

    /**
     * @Route("/", name="page_index")
     * @return Response
     */
    public function index(): Response
    {
        return $this->redirectToRoute("page_authors_list");
    }

    /**
     * @Route("/list/authors", name="page_authors_list")
     * @return Response
     */
    public function authorsList(): Response
    {
        $authors = $this->authorManager->findAll();

        return $this->render("page/author-list.html.twig", [
            "title"   => "Authors",
            "authors" => $authors,
        ]);
    }

    /**
     * @Route("/create/author", name="page_author_create")
     * @param Request $request
     * @return Response
     * @throws Exception
     */
    public function authorCreate(Request $request): Response
    {
        $author = new Author();
        $form = $this->createForm(AuthorType::class, $author);
        $form->handleRequest($request);

        if ($form->isSubmitted() && !$form->isValid()) {
            $errors = $form->getErrors(true);

            for ($i = 0, $cnt = $errors->count(); $i < $cnt; $i++) {
                $this->addError($errors->offsetGet($i)->getMessage());
            }
        } else if ($form->isSubmitted()) {
            $author = $form->getData();
            $this->authorManager->create($author->getName());

            return $this->redirectToRoute("page_authors_list");
        }

        return $this->render("page/author-form.html.twig", [
            "title"  => "Add new author",
            "author" => $form->createView(),
        ]);
    }

    /**
     * @Route("/edit/author/{id}", name="page_author_edit")
     * @param Request $request
     * @param string $id
     * @return Response
     * @throws Exception
     */
    public function authorEdit(Request $request, string $id)
    {
        $author = $this->authorManager->getById($id);
        $form = $this->createForm(AuthorType::class, $author);
        $form->handleRequest($request);

        if ($form->isSubmitted() && !$form->isValid()) {
            $errors = $form->getErrors(true);

            for ($i = 0, $cnt = $errors->count(); $i < $cnt; $i++) {
                $this->addError($errors->offsetGet($i)->getMessage());
            }
        } else if ($form->isSubmitted()) {
            $author = $form->getData();
            $this->authorManager->edit($author->getId(), $author->getName());

            return $this->redirectToRoute("page_authors_list");
        }

        return $this->render("page/author-form.html.twig", [
            "title"  => sprintf("Edit %s", $author->getName()),
            "author" => $form->createView(),
        ]);
    }

    /**
     * @Route("/delete/author/{id}", name="page_author_delete")
     * @param string $id
     * @return Response
     * @throws Exception
     */
    public function authorDelete(string $id): Response
    {
        $this->authorManager->delete($id);

        return $this->redirectToRoute("page_authors_list");
    }

    /**
     * @Route("/list/books", name="page_books_list")
     * @return Response
     */
    public function booksList(): Response
    {
        $books = $this->bookManager->findAll();

        return $this->render("page/book-list.html.twig", [
            "title" => "Books",
            "books" => $books,
        ]);
    }

    /**
     * @Route("/create/book", name="page_book_create")
     * @param Request $request
     * @return Response
     * @throws Exception
     */
    public function bookCreate(Request $request): Response
    {
        $book = new Book();
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && !$form->isValid()) {
            $errors = $form->getErrors(true);

            for ($i = 0, $cnt = $errors->count(); $i < $cnt; $i++) {
                $this->addError($errors->offsetGet($i)->getMessage());
            }
        } else if ($form->isSubmitted()) {
            if (!$form->isValid()) {
                $cause = $form->getErrors(true)->current()->getCause();

                if ($cause->getCause()) {
                    $cause = $cause->getCause();
                }

                $message = $cause->getMessage();
                throw new Exception($message, Response::HTTP_BAD_REQUEST);
            }
            $book = $form->getData();
            $this->bookManager->create(
                $book->getName(),
                $book->getAuthor(),
                $book->getPrice()
            );

            return $this->redirectToRoute("page_books_list");
        }

        return $this->render("page/book-form.html.twig", [
            "title" => "Add new book",
            "book"  => $form->createView(),
        ]);
    }

    /**
     * @Route("/edit/book/{id}", name="page_book_edit")
     * @param Request $request
     * @param string $id
     * @return Response
     * @throws Exception
     */
    public function bookEdit(Request $request, string $id): Response
    {
        $book = $this->bookManager->getById($id);
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && !$form->isValid()) {
            $errors = $form->getErrors(true);

            for ($i = 0, $cnt = $errors->count(); $i < $cnt; $i++) {
                $this->addError($errors->offsetGet($i)->getMessage());
            }
        } else if ($form->isSubmitted()) {
            $book = $form->getData();
            $this->bookManager->edit(
                $book->getId(),
                $book->getName(),
                $book->getAuthor(),
                $book->getPrice()
            );

            return $this->redirectToRoute("page_books_list");
        }

        return $this->render("page/book-form.html.twig", [
            "title" => sprintf("Edit book \"%s\" (%s)", $book->getName(), $book->getAuthor()->getName()),
            "book"  => $form->createView(),
        ]);
    }

    /**
     * @Route("/delete/book/{id}", name="page_book_delete")
     * @param string $id
     * @return Response
     * @throws Exception
     */
    public function bookDelete(string $id): Response
    {
        $this->bookManager->delete($id);

        return $this->redirectToRoute("page_books_list");
    }

    /**
     * @param string $message
     */
    protected function addError(string $message): void
    {
        $request = $this->requestStack->getCurrentRequest();

        if ($request instanceof Request) {
            $session = $request->getSession();
            $errors = $session->get(MainDataProvider::SESSION_ERROR_KEY, []);
            $errors[] = $message;
            $session->set(MainDataProvider::SESSION_ERROR_KEY, $errors);
        }
    }

    /**
     * @return array
     */
    protected function getErrors(): array
    {
        $request = $this->requestStack->getCurrentRequest();

        if (!$request instanceof Request) {
            return [];
        }

        $session = $request->getSession();
        $errors = $session->get(MainDataProvider::SESSION_ERROR_KEY, []);
        $session->set(MainDataProvider::SESSION_ERROR_KEY, []);

        return $errors;
    }

    /**
     * @param string $name
     * @param array $params
     * @param Response|null $response
     * @return Response
     */
    protected function render(string $name, array $params = [], ?Response $response = null): Response
    {
        $params["errors"] = $this->getErrors();

        return parent::render($name, $params);
    }
}
