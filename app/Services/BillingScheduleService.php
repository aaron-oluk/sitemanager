<?php

namespace App\Services;

use Carbon\Carbon;

class BillingScheduleService
{
    public function now(): Carbon
    {
        return Carbon::now();
    }

    public function calculateEndDate(Carbon|string $startDate, int $durationMonths): Carbon
    {
        return Carbon::parse($startDate)->addMonthsNoOverflow($durationMonths);
    }

    public function durationMonthsForHostingPlan(string $hostingPlan): int
    {
        return match ($hostingPlan) {
            'monthly' => 1,
            'quarterly' => 3,
            'biannual' => 6,
            'annual' => 12,
            'biennial' => 24,
            'triennial' => 36,
            default => 1,
        };
    }

    public function paymentCount(string $billingFrequency, int $durationMonths): int
    {
        return $billingFrequency === 'monthly'
            ? max(1, $durationMonths)
            : max(1, (int) ceil($durationMonths / 12));
    }

    public function paymentAmount(float $totalCost, int $paymentCount): float
    {
        return $paymentCount > 0
            ? round($totalCost / $paymentCount, 2)
            : round($totalCost, 2);
    }
}