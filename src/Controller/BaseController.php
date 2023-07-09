<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Contracts\Translation\TranslatorInterface;

abstract class BaseController extends AbstractController
{
    /**
     * @param TranslatorInterface $translator
     */
    public function __construct(
        protected readonly TranslatorInterface $translator
    ) {}
}
