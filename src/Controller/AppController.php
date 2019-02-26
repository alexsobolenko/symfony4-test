<?php

// src/Controller/AppController.php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AppController extends AbstractController {

  public function index() {
    return $this->redirect("/list/authors");
  }

  public function authorsList() {
    $options = [
      "title" => "authorsList",
      "content" => "authorsList"
    ];
    return $this->render("author-list.html.twig", $options);
  }

  public function authorEdit($id) {
    $options = [
      "title" => "authorEdit",
      "content" => "authorEdit"
    ];
    return $this->render("author-form.html.twig", $options);
  }

  public function authorCreate() {
    $options = [
      "title" => "authorCreate",
      "content" => "authorCreate"
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
    $options = [
      "title" => "booksList",
      "content" => "booksList"
    ];
    return $this->render("book-list.html.twig", $options);
  }

  public function bookEdit($id) {
    $options = [
      "title" => "bookEdit",
      "content" => "bookEdit"
    ];
    return $this->render("book-form.html.twig", $options);
  }

  public function bookCreate() {
    $options = [
      "title" => "bookCreate",
      "content" => "bookCreate"
    ];
    return $this->render("book-form.html.twig", $options);
  }

  public function bookDelete($id) {
    return $this->redirect("/list/books");
  }

}