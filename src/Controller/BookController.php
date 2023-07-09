<?php

declare(strict_types=1);

namespace App\Controller;

use App\Exception\AppException;
use App\Manager\BookManager;
use App\Model\BookModel;
use App\Form\BookType;
use App\Model\PaginatedDataModel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/books')]
class BookController extends BaseController
{
    #[Route(path: '/list', name: 'app_books_list', methods: ['GET'])]
    public function booksListAction(Request $request, BookManager $manager): Response
    {
        try {
            $filters = $request->query->all();
            $filters['limit'] = (int) $this->getParameter('books_on_page');
            $books = $manager->findBy($filters);
        } catch (AppException $e) {
            $books = new PaginatedDataModel();
            $this->addFlash('danger', $this->translator->trans($e->getMessage()));
        }

        return $this->render('book/list.html.twig', [
            'title' => $this->translator->trans('title.books.list'),
            'books' => $books,
            'request' => $request,
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
    #[
        Route(path: '/create', name: 'app_book_create', methods: ['GET', 'POST']),
        Route(path: '/edit/{id}', name: 'app_book_edit', methods: ['GET', 'POST'])
    ]
    public function bookEditAction(Request $request, BookManager $manager, ?string $id = null): Response
    {
        try {
            if ($id === null) {
                $model = new BookModel();
                $title = $this->translator->trans('title.books.create');
            } else {
                $book = $manager->get($id);
                $model = BookModel::map($book);
                $title = $this->translator->trans('title.books.edit', [
                    '%name%' => $book->getName(),
                    '%author%' => $book->getAuthor()->getName(),
                ]);
            }

            $form = $this->createForm(BookType::class, $model);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $model = $form->getData();

                if ($id === null) {
                    $book = $manager->create($model->author, $model->name, $model->price);
                    $this->addFlash('success', "Book \"{$book->getNameWithAuthor()}\" created");
                } else {
                    $book = $manager->edit($id, $model->author, $model->name, $model->price);
                    $this->addFlash('success', "Book \"{$book->getNameWithAuthor()}\" saved");
                }

                return $this->redirectToRoute('app_books_list');
            }
        } catch (AppException $e) {
            $this->addFlash('danger', $this->translator->trans($e->getMessage()));

            return $this->redirectToRoute('app_books_list');
        }

        return $this->render('book/form.html.twig', [
            'title' => $title,
            'form' => $form->createView(),
        ]);
    }

    #[Route(path: '/delete/{id}', name: 'app_book_delete', methods: ['GET', 'POST'])]
    public function bookDeleteAction(BookManager $manager, string $id): Response
    {
        try {
            $manager->delete($id);
        } catch (AppException $e) {
            $this->addFlash('danger', $this->translator->trans($e->getMessage()));
        }

        return $this->redirectToRoute('app_books_list');
    }
}
