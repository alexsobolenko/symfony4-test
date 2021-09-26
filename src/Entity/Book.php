<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BookRepository")
 * @ORM\Table(name="`books`")
 */
class Book
{
    /**
     * @var string
     * @ORM\Column(name="`id`", type="guid")
     * @ORM\Id()
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(name="`name`", type="string", length=255)
     */
    private $name;

    /**
     * @var Author
     * @ORM\ManyToOne(targetEntity="Author", inversedBy="books")
     * @ORM\JoinColumn(name="`author_id`", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $author;

    /**
     * @var float
     * @ORM\Column(name="`price`", type="float")
     */
    private $price;

    /**
     * @param Author $author
     * @param string $name
     * @param float $price
     */
    public function __construct(Author $author, string $name, float $price)
    {
        $this->id = Uuid::uuid4()->toString();
        $this->author = $author;
        $this->name = $name;
        $this->price = $price;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return Author
     */
    public function getAuthor(): Author
    {
        return $this->author;
    }

    /**
     * @param Author $author
     */
    public function setAuthor(Author $author): void
    {
        $this->author = $author;
    }

    /**
     * @return float
     */
    public function getPrice(): float
    {
        return $this->price;
    }

    /**
     * @param float $price
     */
    public function setPrice(float $price): void
    {
        $this->price = $price;
    }
}
