<?php

declare(strict_types=1);

namespace App\Controller;

use App\Manager\BookManager;
use App\Model\BookModel;
use App\Form\BookType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(path="/books")
 */
class BookController extends AbstractController
{
    /**
     * @Route(path="/list", methods={"GET"}, name="app_books_list")
     * @param BookManager $manager
     * @return Response
     */
    public function booksListAction(BookManager $manager): Response
    {
        try {
            $books = $manager->findAll();
        } catch (\Throwable $e) {
            $books = [];
            $this->addFlash('danger', $e->getMessage());
        }

        return $this->render('book/list.html.twig', [
            'title' => 'Books',
            'books' => $books,
        ]);
    }

    /**
     * @Route(path="/create", methods={"GET","POST"}, name="app_book_create")
     * @Route(path="/edit/{id}", methods={"GET","POST"}, name="app_book_edit")
     * @param Request $request
     * @param BookManager $manager
     * @param string|null $id
     * @return Response
     */
    public function bookEditAction(Request $request, BookManager $manager, ?string $id = null): Response
    {
        try {
            if ($id === null) {
                $model = new BookModel();
                $title = 'Add new book';
            } else {
                $book = $manager->get($id);
                $model = BookModel::map($book);
                $title = 'Edit ' . $book->getName() . ' (' . $book->getAuthor()->getName() . ')';
            }

            $form = $this->createForm(BookType::class, $model, [
                'em' => $this->getDoctrine()->getManager(),
            ]);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $model = $form->getData();

                if ($id === null) {
                    $book = $manager->create($model->author, $model->name, $model->price);
                    $bookTitle = $book->getName() . ' (' . $book->getAuthor()->getName() . ')';
                    $this->addFlash('success', 'Book "' . $bookTitle . '" created');
                } else {
                    $book = $manager->edit($id, $model->author, $model->name, $model->price);
                    $bookTitle = $book->getName() . ' (' . $book->getAuthor()->getName() . ')';
                    $this->addFlash('success', 'Book "' . $bookTitle . '" saved');
                }

                return $this->redirectToRoute('app_books_list');
            }
        } catch (\Throwable $e) {
            $this->addFlash('danger', $e->getMessage());
        }

        return $this->render('book/form.html.twig', [
            'title' => $title,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route(path="/delete/{id}", methods={"GET","POST"}, name="app_book_delete")
     * @param string $id
     * @param BookManager $manager
     * @return Response
     */
    public function bookDeleteAction(BookManager $manager, string $id): Response
    {
        try {
            $manager->delete($id);
        } catch (\Throwable $e) {
            $this->addFlash('danger', $e->getMessage());
        }

        return $this->redirectToRoute('app_books_list');
    }
}
