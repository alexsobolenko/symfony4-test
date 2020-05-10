<?php

declare(strict_types=1);

namespace App\EventListener;

use App\DataProvider\MainDataProvider;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ExceptionListener
{
    /** @var UrlGeneratorInterface */
    protected $router;

    /**
     * ExceptionListener constructor.
     * @param UrlGeneratorInterface $router
     */
    public function __construct(UrlGeneratorInterface $router)
    {
        $this->router = $router;
    }

    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();
        $request = $event->getRequest();
        $session = $request->getSession();
        $errors = $session->get(MainDataProvider::SESSION_ERROR_KEY, []);
        $errors[] = $exception->getMessage();
        $session->set(MainDataProvider::SESSION_ERROR_KEY, $errors);
        $pathInfo = $this->router->matchRequest($request);
        $sourceName = array_key_exists("_route", $pathInfo) ? $pathInfo["_route"] : "";
        $targetName = MainDataProvider::getTargetName($sourceName);
        $response = new RedirectResponse($this->router->generate($targetName));
        $event->setResponse($response);
    }
}
