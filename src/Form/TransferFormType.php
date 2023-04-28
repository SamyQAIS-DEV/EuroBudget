<?php

namespace App\Form;

use App\Dto\TransferDto;
use App\Entity\DepositAccount;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TransferFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('fromDepositAccount', EntityType::class, [
                'class' => DepositAccount::class,
                'choice_label' => 'title',
                'placeholder' => 'Compte débité',
            ])
            ->add('targetDepositAccount', EntityType::class, [
                'class' => DepositAccount::class,
                'choice_label' => 'title',
                'placeholder' => 'Compte crédité',
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
