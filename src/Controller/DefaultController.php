<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends BaseController
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
     * @Route(path="/locale/{locale}", methods={"GET"}, name="app_change_locale")
     * @param Request $request
     * @param string $locale
     * @return Response
     */
    public function changeLocaleAction(Request $request, string $locale): Response
    {
        $request->getSession()->set('_locale', $locale);
        $previousUrl = $request->server->get('HTTP_REFERER');

        return $this->redirect($previousUrl);
    }
}
