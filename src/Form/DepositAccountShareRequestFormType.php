<?php

namespace App\Form;

use App\Dto\DepositAccountShareRequestDto;
use App\Entity\DepositAccount;
use App\Repository\DepositAccountRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DepositAccountShareRequestFormType extends AbstractType
{
    public function __construct(
        private readonly Security $security,
        private readonly DepositAccountRepository $depositAccountRepository,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var DepositAccountShareRequestDto $entity */
        $entity = $builder->getData();
        $user = $this->security->getUser();
        $choices = $this->depositAccountRepository->findForAndWithout($user, $entity->user);

        $builder->add('depositAccount', EntityType::class, [
            'class' => DepositAccount::class,
            'placeholder' => 'Compte en banque',
            'choices' => $choices,
            'choice_label' => 'title',
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => DepositAccountShareRequestDto::class,
        ]);
    }
}
