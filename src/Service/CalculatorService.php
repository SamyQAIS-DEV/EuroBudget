<?php

namespace App\Service;

use App\Entity\CalculableInterface;
use App\Enum\TypeEnum;
use App\Exception\CalculatorException;

class CalculatorService
{
    public function __construct()
    {
    }

    public function calculate(CalculableInterface $item = null, CalculableInterface $originalItem = null): float
    {
        // Deleted
        if ($item === null && $originalItem) {
            return $this->calculateForDeletedItem($originalItem);
        }
        // Updated
        if ($item && $originalItem) {
            return $this->calculateForUpdatedItem($item, $originalItem);
        }
        // Created
        if ($item && $originalItem === null) {
            return $this->calculateForCreatedItem($item);
        }
        throw new CalculatorException('Calculator Service Exception');
    }

    private function calculateForCreatedItem(CalculableInterface $item): float
    {
        if ($item->isPast()) {
            return $item->getType() === TypeEnum::DEBIT ? -$item->getAmount() : $item->getAmount();
        }

        return 0;
    }

    private function calculateForUpdatedItem(CalculableInterface $item, CalculableInterface $originalItem): float
    {
        if ($item->isPast() && $originalItem->isPast()) {
            if ($item->getType() === TypeEnum::DEBIT && $originalItem->getType() === TypeEnum::DEBIT) {
                return $originalItem->getAmount() - $item->getAmount();
            }
            if ($item->getType() === TypeEnum::DEBIT && $originalItem->getType() === TypeEnum::CREDIT) {
                return -($originalItem->getAmount() + $item->getAmount());
            }
            if ($item->getType() === TypeEnum::CREDIT && $originalItem->getType() === TypeEnum::DEBIT) {
                return $originalItem->getAmount() + $item->getAmount();
            }
            if ($item->getType() === TypeEnum::CREDIT && $originalItem->getType() === TypeEnum::CREDIT) {
                return -($originalItem->getAmount() - $item->getAmount());
            }
        }
        if ($item->isPast() && !$originalItem->isPast()) {
            if ($item->getType() === TypeEnum::DEBIT && $originalItem->getType() === TypeEnum::DEBIT) {
                return -$item->getAmount();
            }
            if ($item->getType() === TypeEnum::DEBIT && $originalItem->getType() === TypeEnum::CREDIT) {
                return -$item->getAmount();
            }
            if ($item->getType() === TypeEnum::CREDIT && $originalItem->getType() === TypeEnum::DEBIT) {
                return $item->getAmount();
            }
            if ($item->getType() === TypeEnum::CREDIT && $originalItem->getType() === TypeEnum::CREDIT) {
                return $item->getAmount();
            }
        }
        if (!$item->isPast() && $originalItem->isPast()) {
            if ($item->getType() === TypeEnum::DEBIT && $originalItem->getType() === TypeEnum::DEBIT) {
                return $originalItem->getAmount();
            }
            if ($item->getType() === TypeEnum::DEBIT && $originalItem->getType() === TypeEnum::CREDIT) {
                return -$originalItem->getAmount();
            }
            if ($item->getType() === TypeEnum::CREDIT && $originalItem->getType() === TypeEnum::DEBIT) {
                return $originalItem->getAmount();
            }
            if ($item->getType() === TypeEnum::CREDIT && $originalItem->getType() === TypeEnum::CREDIT) {
                return -$originalItem->getAmount();
            }
        }

        return 0;
    }

    private function calculateForDeletedItem(CalculableInterface $item): float
    {
        if ($item->isPast()) {
            return $item->getType() === TypeEnum::DEBIT ? $item->getAmount() : -$item->getAmount();
        }

        return 0;
    }
}