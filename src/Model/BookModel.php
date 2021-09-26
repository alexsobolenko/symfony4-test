<?php

declare(strict_types=1);

namespace App\Model;

use App\Entity\Book;
use Symfony\Component\Validator\Constraints as Assert;

class BookModel
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

    /**
     * @var float|null
     * @Assert\NotBlank(message="Name should not be blank")
     * @Assert\Range(
     *      min=0.1,
     *      max=1000000,
     *      minMessage="Price cannot be less than ${{ limit }}",
     *      maxMessage="Price cannot be greater than ${{ limit }}"
     * )
     */
    public $price;

    /**
     * @var string|null
     * @Assert\NotBlank(message="Author should not be blank")
     */
    public $author;

    public function __construct()
    {
        $this->name = null;
        $this->price = null;
        $this->author = null;
    }

    /**
     * @param Book $entity
     * @return BookModel
     */
    public static function map(Book $entity): BookModel
    {
        $model = new self();
        $model->name = $entity->getName();
        $model->price = $entity->getPrice();
        $model->author = $entity->getAuthor()->getId();

        return $model;
    }
}
