<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Author;
use App\Model\BookModel;
use App\Repository\AuthorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BookType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var EntityManagerInterface $em */
        $em = $options['em'];

        /** @var AuthorRepository $repo */
        $repo = $em->getRepository(Author::class);

        if (!$repo->hasAuthors()) {
            throw new BadRequestException('Has no authors yet. Create one new first');
        }

        $authors = [];
        foreach ($repo->findBy([], ['name' => 'ASC']) as $author) {
            $authors[$author->getName()] = $author->getId();
        }

        $builder
            ->add('author', ChoiceType::class, [
                'required' => true,
                'label' => 'Author',
                'choices' => $authors,
            ])
            ->add('name', TextType::class, [
                'required' => true,
                'label' => 'Book name',
            ])
            ->add('price', NumberType::class, [
                'required' => true,
                'label' => 'Price',
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Save',
            ])
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired('em');
        $resolver->setDefaults([
            'data_class' => BookModel::class,
        ]);
    }
}
