<?php

declare(strict_types=1);

namespace App\Controller;

use App\Exception\AppException;
use App\Model\AuthorModel;
use App\Form\AuthorType;
use App\Model\PaginatedDataModel;
use App\Repository\AuthorRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route(path: '/authors')]
class AuthorController extends AbstractController
{
    #[Route(path: '/list', name: 'app_authors_list', methods: ['GET'])]
    public function list(Request $request, AuthorRepository $repository, TranslatorInterface $translator): Response
    {
        $filters = $request->query->all();
        if (!array_key_exists('page', $filters)) {
            $filters['page'] = 1;
        }
        if (!array_key_exists('limit', $filters)) {
            $filters['limit'] = (int) $this->getParameter('authors_on_page');
        }

        try {
            $total = $repository->countByFilter($filters);
            $items = $repository->findByFilter($filters);
        } catch (AppException $e) {
            $total = 0;
            $items = [];
            $this->addFlash('danger', $translator->trans($e->getMessage()));
        }

        return $this->render('author/list.html.twig', [
            'title' => $translator->trans('title.authors.list'),
            'authors' => new PaginatedDataModel($total, $filters['limit'], $filters['page'], $items),
        ]);
    }

    #[Route(path: '/create', name: 'app_author_create', methods: ['GET', 'POST'])]
    #[Route(path: '/edit/{id}', name: 'app_author_edit', methods: ['GET', 'POST'])]
    public function details(
        Request $request,
        AuthorRepository $repository,
        TranslatorInterface $translator,
        ?string $id = null
    ): Response {
        try {
            if ($id === null) {
                $model = new AuthorModel();
                $title = $translator->trans('title.authors.create');
            } else {
                $author = $repository->get($id);
                $model = AuthorModel::map($author);
                $title = $translator->trans('title.authors.edit', [
                    '%name%' => $author->getName(),
                ]);
            }

            $form = $this->createForm(AuthorType::class, $model);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $model = $form->getData();
                $author = $id === null
                    ? $repository->create($model->name)
                    : $repository->edit($id, $model->name);
                $this->addFlash('success', $translator->trans('message.author.saved', [
                    '%name%' => $author->getName(),
                ]));

                return $this->redirectToRoute('app_authors_list');
            }
        } catch (AppException $e) {
            $this->addFlash('danger', $translator->trans($e->getMessage()));

            return $this->redirectToRoute('app_authors_list');
        }

        return $this->render('author/form.html.twig', [
            'title' => $title,
            'form' => $form->createView(),
        ]);
    }

    #[Route(path: '/delete/{id}', name: 'app_author_delete', methods: ['GET', 'POST'])]
    public function delete(AuthorRepository $repository, TranslatorInterface $translator, string $id): Response
    {
        try {
            $author = $repository->delete($id);
            $this->addFlash('success', $translator->trans('message.author.deleted', [
                '%name%' => $author->getName(),
            ]));
        } catch (AppException $e) {
            $this->addFlash('danger', $translator->trans($e->getMessage()));
        }

        return $this->redirectToRoute('app_authors_list');
    }
}
