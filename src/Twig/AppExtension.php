<?php

declare(strict_types=1);

namespace App\Twig;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    /**
     * @var RequestStack
     */
    private RequestStack $requestStack;

    /**
     * @var UrlGeneratorInterface
     */
    private UrlGeneratorInterface $router;

    /**
     * @param RequestStack $requestStack
     * @param UrlGeneratorInterface $router
     */
    public function __construct(RequestStack $requestStack, UrlGeneratorInterface $router)
    {
        $this->requestStack = $requestStack;
        $this->router = $router;
    }

    /**
     * @return TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('append_param', [$this, 'appendParamToRoute']),
        ];
    }

    /**
     * @param string $paramName
     * @param mixed $paramValue
     * @param array $paramsFromRequest
     * @return string
     */
    public function appendParamToRoute(string $paramName, $paramValue, array $paramsFromRequest = []): string
    {
        $request = $this->requestStack->getCurrentRequest();
        $params = [];
        foreach ($paramsFromRequest as $key) {
            $value = $request->get($key);
            if ($value !== null && $value !== '') {
                $params[$key] = $value;
            }
        }

        $params[$paramName] = $paramValue;

        return $this->router->generate(
            $request->get('_route'),
            $params,
            UrlGeneratorInterface::ABSOLUTE_URL
        );
    }
}
