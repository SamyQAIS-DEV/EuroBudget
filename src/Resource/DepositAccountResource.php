<?php

namespace App\Resource;

use App\Entity\DepositAccount;
use Symfony\Component\Serializer\Annotation\Groups;

class DepositAccountResource
{
    #[Groups(['read'])]
    public int $id;

    #[Groups(['read'])]
    public string $title;

    #[Groups(['read'])]
    public float $amount;

    #[Groups(['read'])]
    public int $creatorId;

    #[Groups(['read'])]
    public string $color;

    #[Groups(['read'])]
    public int $waitingOperationsNb;

    #[Groups(['read'])]
    public float $waitingAmount;

    #[Groups(['read'])]
    public float $finalAmount;

    /**
     * Garde une trace de l'entité qui a servi à créer la resource.
     */
    public DepositAccount $entity;

    public static function fromDepositAccount(
        DepositAccount $depositAccount,
        ?int $waitingOperationsNb,
        ?float $waitingAmount
    ): self {
        $resource = new self();
        $creator = $depositAccount->getCreator();
        $resource->id = $depositAccount->getId();
        $resource->title = $depositAccount->getTitle();
        $resource->amount = $depositAccount->getAmount();
        $resource->creatorId = $creator->getId();
        $resource->color = $depositAccount->getColor();
        $resource->waitingOperationsNb = $waitingOperationsNb ?? 0;
        $resource->waitingAmount = $waitingAmount ?? 0;
        $resource->finalAmount = $resource->amount + $resource->waitingAmount;
        $resource->entity = $depositAccount;

        return $resource;
    }
}