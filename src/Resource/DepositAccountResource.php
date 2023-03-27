<?php

namespace App\Resource;

use App\Entity\DepositAccount;
use Symfony\Component\Serializer\Annotation\Groups;

class DepositAccountResource
{
    #[Groups(['read'])]
    public ?int $id = null;

    #[Groups(['read'])]
    public ?string $title = '';

    #[Groups(['read'])]
    public ?float $amount = null;

    #[Groups(['read'])]
    public ?int $creatorId = null;

    #[Groups(['read'])]
    public ?string $color = '';

    #[Groups(['read'])]
    public ?int $waitingOperationsNb = null;

    #[Groups(['read'])]
    public ?float $waitingAmount = null;

    #[Groups(['read'])]
    public ?float $finalAmount = null;

    /**
     * Garde une trace de l'entité qui a servi à créer la resource.
     */
    public ?DepositAccount $entity = null;

    public static function fromDepositAccount(
        DepositAccount $depositAccount,
        int $waitingOperationsNb,
        float $waitingAmount
    ): self {
        $resource = new self();
        $creator = $depositAccount->getCreator();
        $resource->id = $depositAccount->getId();
        $resource->title = $depositAccount->getTitle();
        $resource->amount = $depositAccount->getAmount();
        $resource->creatorId = $creator->getId();
        $resource->color = $depositAccount->getColor();
        $resource->waitingOperationsNb = $waitingOperationsNb;
        $resource->waitingAmount = $waitingAmount;
        $resource->finalAmount = $resource->amount - $resource->waitingAmount;
        $resource->entity = $depositAccount;

        return $resource;
    }
}