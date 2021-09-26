<?php

declare(strict_types=1);

namespace App\Model;

use App\Entity\Author;
use Symfony\Component\Validator\Constraints as Assert;

class AuthorModel
{
    /**
     * @var string|null
     * @Assert\NotBlank(message="Name should not be blank")
     * @Assert\Length(
     *      min=2,
     *      max=100,
     *      minMessage="Name must be at least {{ limit }} characters long",
     *      maxMessage="Name cannot be longer than {{ limit }} characters"
     * )
     */
    public $name;

    public function __construct()
    {
        $this->name = null;
    }

    /**
     * @param Author $entity
     * @return AuthorModel
     */
    public static function map(Author $entity): AuthorModel
    {
        $model = new self();
        $model->name = $entity->getName();

        return $model;
    }
}
