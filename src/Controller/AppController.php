<?php

// src/Controller/AppController.php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use App\Entity\Author;
use App\Entity\Book;

class AppController extends AbstractController {

  public function index() {
    return $this->redirect("/list/authors");
  }

  public function authorsList() {
    $authors = $this->getDoctrine()->getRepository(Author::class)->findAll();
    $options = [
      "title" => "Authors",
      "authors" => $authors
    ];
    return $this->render("author-list.html.twig", $options);
  }

  public function authorEdit($id) {
    $author = $this->getDoctrine()->getRepository(Author::class)->find($id);
    $options = [
      "title" => "Edit ".$author->getName(),
      "mode" => "edit",
      "author" => $author
    ];
    return $this->render("author-form.html.twig", $options);
  }

  public function authorCreate() {
    $options = [
      "title" => "Add new author",
      "mode" => "create",
      "author" => [ "id" => "", "name" => "", "books" => "" ]
    ];
    return $this->render("author-form.html.twig", $options);
  }

  public function authorDelete($id) {
    return $this->redirect("/list/authors");
  }

  public function authorBookDelete($bookId, $authorId) {
    return $this->redirect("/author/$authorId");
  }

  public function booksList() {
    $altBooks = [];
    $books = $this->getDoctrine()->getRepository(Book::class)->findAll();
    foreach ($books as $book) {
      $author = $this->getDoctrine()->getRepository(Author::class)->find($book->getId());
      $altBooks[] = [
        "id" => $book->getId(),
        "name" => $book->getName(),
        "author" => $author->getName(),
        "price" => $book->getPrice()
      ];
    }
    $options = [
      "title" => "booksList",
      "books" => $altBooks
    ];
    return $this->render("book-list.html.twig", $options);
  }

  public function bookEdit($id) {
    $book = $this->getDoctrine()->getRepository(Book::class)->find($id);
    $author = $this->getDoctrine()->getRepository(Author::class)->find($book->getId());
    $authors = $this->getDoctrine()->getRepository(Author::class)->findAll();
    $options = [
      "title" => "Edit book \"".$book->getName()."\" (".$author->getName().")",
      "mode" => "edit",
      "book" => [ "id" => $book->getId(), "name" => $book->getName(), "author" => $author->getName(), "price" => $book->getPrice() ],
      "authors" => $authors
    ];
    return $this->render("book-form.html.twig", $options);
  }

  public function bookCreate() {
    $authors = $this->getDoctrine()->getRepository(Author::class)->findAll();
    $options = [
      "title" => "Add new book",
      "mode" => "create",
      "book" => [ "id" => "", "name" => "", "author" => "", "price" => "" ],
      "authors" => $authors
    ];
    return $this->render("book-form.html.twig", $options);
  }

  public function bookDelete($id) {
    return $this->redirect("/list/books");
  }

}