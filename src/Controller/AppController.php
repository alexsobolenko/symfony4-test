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
    $entityManager = $this->getDoctrine()->getManager();
    $author = $this->getDoctrine()->getRepository(Author::class)->find($id);
    $entityManager->remove($author);
    $entityManager->flush();
    return $this->redirect("/list/authors");
  }

  public function authorSave() {
    $entityManager = $this->getDoctrine()->getManager();
    if ($_POST['action'] === 'insert') {
      $author = new Author();
      $author->setBooks(0);
    }
    else if ($_POST['action'] === 'update') {
      $author = $this->getDoctrine()->getRepository(Author::class)->find($_POST['id']);
    }
    else return Response('oops!');
    $author->setName($_POST['name']);
    $entityManager->persist($author);
    $entityManager->flush();
    return $this->redirect("/list/authors");
  }

  public function booksList() {
    $altBooks = [];
    $books = $this->getDoctrine()->getRepository(Book::class)->findAll();
    foreach ($books as $book) {
      $author = $this->getDoctrine()->getRepository(Author::class)->find($book->getAuthor());
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
    $author = $this->getDoctrine()->getRepository(Author::class)->find($book->getAuthor());
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
    $entityManager = $this->getDoctrine()->getManager();
    $book = $this->getDoctrine()->getRepository(Book::class)->find($id);
    $entityManager->remove($book);
    $entityManager->flush();
    return $this->redirect("/list/books");
  }

  public function bookSave() {
    $entityManager = $this->getDoctrine()->getManager();
    if ($_POST['action'] === 'insert') {
      $book = new Book();
    }
    else if ($_POST['action'] === 'update') {
      $book = $this->getDoctrine()->getRepository(Book::class)->find($_POST['id']);
    }
    else return Response('oops!');
    $book->setName($_POST['name']);
    $book->setPrice($_POST['price']);
    $book->setAuthor($_POST['author']);
    $entityManager->flush();
    $entityManager->persist($book);
    if ($_POST['action'] === 'insert') {
      $author = $this->getDoctrine()->getRepository(Author::class)->find($_POST['author']);
      $author->setBooks($author->getBooks() + 1);
      $entityManager->persist($author);
      $entityManager->flush();
    }
    return $this->redirect("/list/books");
  }

}