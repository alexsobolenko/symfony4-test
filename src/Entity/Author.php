<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\AuthorRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: AuthorRepository::class), ORM\Table(name: 'authors')]
class Author
{
    #[ORM\Id, ORM\Column(name: 'id', type: 'guid')]
    private string $id;

    #[ORM\Column(name: 'name', type: 'string', length: 255)]
    private string $name;

    #[ORM\OneToMany(mappedBy: 'author', targetEntity: Book::class)]
    private Collection $books;

    /**
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->id = Uuid::v7()->toRfc4122();
        $this->name = $name;
        $this->books = new ArrayCollection();
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
     * @return Collection
     */
    public function getBooks(): Collection
    {
        return $this->books;
    }

    /**
     * @param Book $book
     */
    public function addBook(Book $book): void
    {
        if (!$this->books->contains($book)) {
            $this->books->add($book);
            $book->setAuthor($this);
        }
    }

    /**
     * @param Book $book
     */
    public function removeBook(Book $book): void
    {
        if ($this->books->contains($book)) {
            $this->books->removeElement($book);
        }
    }
}
