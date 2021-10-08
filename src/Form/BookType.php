<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Author;
use App\Exception\AppException;
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
use Symfony\Contracts\Translation\TranslatorInterface;

class BookType extends AbstractType
{
    /**
     * @var TranslatorInterface
     */
    protected TranslatorInterface $translator;

    /**
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

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
            throw new AppException('error.has_no_authors');
        }

        $authors = [];
        foreach ($repo->findBy([], ['name' => 'ASC']) as $author) {
            $authors[$author->getName()] = $author->getId();
        }

        $builder
            ->add('author', ChoiceType::class, [
                'required' => true,
                'label' => $this->translator->trans('page.book.details.author'),
                'choices' => $authors,
            ])
            ->add('name', TextType::class, [
                'required' => true,
                'label' => $this->translator->trans('page.book.details.name'),
            ])
            ->add('price', NumberType::class, [
                'required' => true,
                'label' => $this->translator->trans('page.book.details.price'),
            ])
            ->add('submit', SubmitType::class, [
                'label' => $this->translator->trans('main.save'),
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
