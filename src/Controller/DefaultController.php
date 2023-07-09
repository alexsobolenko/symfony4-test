<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class DefaultController extends BaseController
{
    #[Route(path: '', name: 'app_index', methods: ['GET'])]
    public function indexAction(): Response
    {
        return $this->redirectToRoute('app_authors_list');
    }

    #[Route(path: '/locale/{locale}', name: 'app_change_locale', methods: ['GET'])]
    public function changeLocaleAction(Request $request, string $locale): Response
    {
        $request->getSession()->set('_locale', $locale);
        $previousUrl = $request->server->get('HTTP_REFERER');

        return $this->redirect($previousUrl);
    }
}
