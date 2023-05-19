<?php

namespace App\Form;

use App\Dto\TransferDto;
use App\Entity\DepositAccount;
use App\Repository\DepositAccountRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TransferFormType extends AbstractType
{
    public function __construct(
        private readonly Security $security,
        private readonly DepositAccountRepository $depositAccountRepository,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $user = $this->security->getUser();
        $choices = $this->depositAccountRepository->findFor($user);

        $builder
            ->add('fromDepositAccount', EntityType::class, [
                'label' => 'Compte débité',
                'class' => DepositAccount::class,
                'placeholder' => 'Compte débité',
                'choices' => $choices,
                'choice_label' => fn(DepositAccount $depositAccount) => $depositAccount->getTitle() . ' - ' . $depositAccount->getAmount() . ' €'
            ])
            ->add('targetDepositAccount', EntityType::class, [
                'label' => 'Compte crédité',
                'class' => DepositAccount::class,
                'placeholder' => 'Compte crédité',
                'choices' => $choices,
                'choice_label' => fn(DepositAccount $depositAccount) => $depositAccount->getTitle() . ' - ' . $depositAccount->getAmount() . ' €'
            ])
            ->add('amount', NumberType::class, [
                'label' => 'Montant',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => TransferDto::class,
        ]);
    }
}
