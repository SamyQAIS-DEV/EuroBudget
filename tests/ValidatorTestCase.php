<?php

namespace App\Tests;

use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

/**
 * @template V
 */
class ValidatorTestCase extends KernelTestCase
{
    /**
     * Génère un contexte qui attend (ou non) une violation.
     **/
    protected function getContext(bool $expectedViolation, int $nb = 1): ExecutionContextInterface
    {
        $context = $this->getMockBuilder(ExecutionContextInterface::class)->getMock();
        if ($expectedViolation) {
            $violation = $this->getMockBuilder(ConstraintViolationBuilderInterface::class)->getMock();
            $violation->expects($this->any())->method('setParameter')->willReturn($violation);
            $violation->expects($this->any())->method('setCode')->willReturn($violation);
            $violation->expects($this->any())->method('atPath')->willReturn($violation);
            $violation->expects($this->exactly($nb))->method('addViolation');
            $context
                ->expects($this->exactly($nb))
                ->method('buildViolation')
                ->willReturn($violation);
        } else {
            $context
                ->expects($this->never())
                ->method('buildViolation');
        }

        return $context;
    }
}
