<?php

namespace App\Service;

use App\Constants\CalculationConstants;

class CalculationService
{
    private const MIN_AMOUNT = 1000;
    private const MAX_AMOUNT = 12000;
    private const MIN_INSTALLMENTS = 3;
    private const MAX_INSTALLMENTS = 18;

    public function calculateSchedule(float $amount, int $installments): array
    {
        if ($amount < self::MIN_AMOUNT || $amount > self::MAX_AMOUNT || $amount % 500 !== 0) {
            throw new \InvalidArgumentException('Invalid amount');
        }

        if ($installments < self::MIN_INSTALLMENTS || $installments > self::MAX_INSTALLMENTS || $installments % 3 !== 0) {
            throw new \InvalidArgumentException('Invalid number of installments');
        }

        $result = [];
        $monthlyPayment = $this->calculateMonthlyPayment($amount, $installments);
        $monthlyRate = CalculationConstants::INTEREST_RATE / 12 / 100;
        $remainingAmount = $amount;

        for($i=1; $i<=$installments; $i++)
        {
            $interest = $remainingAmount * $monthlyRate;
            $capitalAmount = $monthlyPayment - $interest;
            $remainingAmount -= $capitalAmount;

            $result[] = [
                'installments' => $i,
                'monthly_payment' => $monthlyPayment,
                'interest' => round($interest, 2),
                'capital_amount' => round($capitalAmount, 2)
            ];
        }

        return $result;
    }

    public function calculateMonthlyPayment(float $amount, int $installments): float
    {
        $monthlyRate = CalculationConstants::INTEREST_RATE / 12 / 100;
        $rate = $amount * (($monthlyRate * pow(1 + $monthlyRate, $installments)) / (pow(1 + $monthlyRate, $installments) - 1));

        return round($rate, 2);
    }
}