<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Invoice;
use App\Form\Type\SwitchType;
use App\Repository\CategoryRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InvoiceFormType extends AbstractType
{
    public function __construct(
        private readonly Security $security,
        private readonly CategoryRepository $categoryRepository,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $user = $this->security->getUser();
        $choices = $this->categoryRepository->findByDepositAccount($user->getFavoriteDepositAccount());

        $builder
            ->add('label', TextType::class, [
                'label' => 'Nom',
            ])
            ->add('amount', NumberType::class, [
                'label' => 'Montant',
            ])
            ->add('active', SwitchType::class);

        $user = $this->security->getUser();

        if ($user && $user->isPremium()) {
            $builder->add('category', EntityType::class, [
                'class' => Category::class,
                'placeholder' => 'Catégorie',
                'choices' => $choices,
                'choice_label' => 'name'
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Invoice::class,
        ]);
    }
}
