<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Author;
use App\Model\AuthorModel;
use App\Form\AuthorType;
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
     * @Route(path="/create", methods={"GET","POST"}, name="app_author_create")
     * @Route(path="/edit/{id}", methods={"GET","POST"}, name="app_author_edit")
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
     * @Route(path="/delete/{id}", methods={"GET","POST"}, name="app_author_delete")
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
}
