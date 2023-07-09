<?php

declare(strict_types=1);

namespace App\Controller;

use App\Exception\AppException;
use App\Model\BookModel;
use App\Form\BookType;
use App\Model\PaginatedDataModel;
use App\Repository\BookRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route(path: '/books')]
class BookController extends AbstractController
{
    #[Route(path: '/list', name: 'app_books_list', methods: ['GET'])]
    public function list(Request $request, BookRepository $repository, TranslatorInterface $translator): Response
    {
        $filters = $request->query->all();
        if (!array_key_exists('page', $filters)) {
            $filters['page'] = 1;
        }
        if (!array_key_exists('limit', $filters)) {
            $filters['limit'] = (int) $this->getParameter('books_on_page');
        }

        try {
            $total = $repository->countByFilter($filters);
            $items = $repository->findByFilter($filters);
        } catch (AppException $e) {
            $total = 0;
            $items = [];
            $this->addFlash('danger', $translator->trans($e->getMessage()));
        }

        return $this->render('book/list.html.twig', [
            'title' => $translator->trans('title.books.list'),
            'books' => new PaginatedDataModel($total, $filters['limit'], $filters['page'], $items),
        ]);
    }

    #[Route(path: '/create', name: 'app_book_create', methods: ['GET', 'POST'])]
    #[Route(path: '/edit/{id}', name: 'app_book_edit', methods: ['GET', 'POST'])]
    public function details(
        Request $request,
        BookRepository $repository,
        TranslatorInterface $translator,
        ?string $id = null
    ): Response {
        try {
            if ($id === null) {
                $model = new BookModel();
                $title = $translator->trans('title.books.create');
            } else {
                $book = $repository->get($id);
                $model = BookModel::map($book);
                $title = $translator->trans('title.books.edit', [
                    '%name%' => $book->getName(),
                    '%author%' => $book->getAuthor()->getName(),
                ]);
            }

            $form = $this->createForm(BookType::class, $model);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $model = $form->getData();
                $book = $id === null
                    ? $repository->create($model->author, $model->name, $model->price)
                    : $repository->edit($id, $model->author, $model->name, $model->price);
                $this->addFlash('success', $translator->trans('message.book.saved', [
                    '%name%' => $book->getNameWithAuthor(),
                ]));

                return $this->redirectToRoute('app_books_list');
            }
        } catch (AppException $e) {
            $this->addFlash('danger', $translator->trans($e->getMessage()));

            return $this->redirectToRoute('app_books_list');
        }

        return $this->render('book/form.html.twig', [
            'title' => $title,
            'form' => $form->createView(),
        ]);
    }

    #[Route(path: '/delete/{id}', name: 'app_book_delete', methods: ['GET', 'POST'])]
    public function delete(BookRepository $repository, TranslatorInterface $translator, string $id): Response
    {
        try {
            $book = $repository->delete($id);
            $this->addFlash('success', $translator->trans('message.book.deleted', [
                '%name%' => $book->getNameWithAuthor(),
            ]));
        } catch (AppException $e) {
            $this->addFlash('danger', $translator->trans($e->getMessage()));
        }

        return $this->redirectToRoute('app_books_list');
    }
}
