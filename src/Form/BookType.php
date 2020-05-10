<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Author;
use App\Entity\Book;
use App\Repository\AuthorRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BookType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add("author", EntityType::class, [
                "class" => Author::class,
                "query_builder"   => function (AuthorRepository $er) {
                    return $er->createQueryBuilder("a")->orderBy("a.name", "ASC");
                },
                "choice_label"    => "name",
                "label"           => "Author",
                "invalid_message" => "Invalid author",
            ])
            ->add("name", TextType::class, [
                "label"           => "Title",
                "invalid_message" => "Invalid title",
            ])
            ->add("price", NumberType::class, [
                "invalid_message" => "Invalid price",
            ])
            ->add("submit", SubmitType::class, [
                "label"           => "Save",
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            "data_class" => Book::class,
        ]);
    }
}
