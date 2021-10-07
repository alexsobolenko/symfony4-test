<?php

declare(strict_types=1);

namespace App\Controller;

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
}
