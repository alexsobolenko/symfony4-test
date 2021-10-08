<?php

declare(strict_types=1);

namespace App\Controller;

use App\Exception\AppException;
use App\Manager\AuthorManager;
use App\Model\AuthorModel;
use App\Form\AuthorType;
use App\Model\PaginatedDataModel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(path="/authors")
 */
class AuthorController extends BaseController
{
    /**
     * @Route(path="/list", methods={"GET"}, name="app_authors_list")
     * @param Request $request
     * @param AuthorManager $manager
     * @return Response
     */
    public function authorsListAction(Request $request, AuthorManager $manager): Response
    {
        try {
            $filters = $request->query->all();
            $filters['limit'] = (int) $this->getParameter('authors_on_page');
            $authors = $manager->findBy($filters);
        } catch (AppException $e) {
            $authors = new PaginatedDataModel();
            $this->addFlash(
                'danger',
                $this->translator->trans($e->getMessage())
            );
        }

        return $this->render('author/list.html.twig', [
            'title' => $this->translator->trans('title.authors.list'),
            'authors' => $authors,
            'request' => $request,
        ]);
    }

    /**
     * @Route(path="/create", methods={"GET","POST"}, name="app_author_create")
     * @Route(path="/edit/{id}", methods={"GET","POST"}, name="app_author_edit")
     * @param Request $request
     * @param AuthorManager $manager
     * @param string|null $id
     * @return Response
     */
    public function authorDetailsAction(Request $request, AuthorManager $manager, ?string $id = null): Response
    {
        try {
            if ($id === null) {
                $model = new AuthorModel();
                $title = $this->translator->trans('title.authors.create');
            } else {
                $author = $manager->get($id);
                $model = AuthorModel::map($author);
                $title = $this->translator->trans('title.authors.edit', [
                    '%name%' => $author->getName(),
                ]);
            }

            $form = $this->createForm(AuthorType::class, $model);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $model = $form->getData();
                if ($id === null) {
                    $author = $manager->create($model->name);
                    $this->addFlash('success', 'Author "' . $author->getName() . '" created');
                } else {
                    $author = $manager->edit($id, $model->name);
                    $this->addFlash('success', 'Author "' . $author->getName() . '" saved');
                }

                return $this->redirectToRoute('app_authors_list');
            }
        } catch (AppException $e) {
            $this->addFlash(
                'danger',
                $this->translator->trans($e->getMessage())
            );

            return $this->redirectToRoute('app_authors_list');
        }

        return $this->render('author/form.html.twig', [
            'title' => $title,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route(path="/delete/{id}", methods={"GET","POST"}, name="app_author_delete")
     * @param AuthorManager $manager
     * @param string $id
     * @return Response
     */
    public function authorDeleteAction(AuthorManager $manager, string $id): Response
    {
        try {
            $manager->delete($id);
            $this->addFlash('success', 'Author deleted');
        } catch (AppException $e) {
            $this->addFlash(
                'danger',
                $this->translator->trans($e->getMessage())
            );
        }

        return $this->redirectToRoute('app_authors_list');
    }
}