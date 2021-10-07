<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Book;
use App\Model\BookModel;
use App\Form\BookType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(path="/books")
 */
class BookController extends BaseController
{
    /**
     * @Route(path="/list", methods={"GET"}, name="app_books_list")
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
     * @Route(path="/create", methods={"GET","POST"}, name="app_book_create")
     * @Route(path="/edit/{id}", methods={"GET","POST"}, name="app_book_edit")
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
     * @Route(path="/delete/{id}", methods={"GET","POST"}, name="app_book_delete")
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
}
