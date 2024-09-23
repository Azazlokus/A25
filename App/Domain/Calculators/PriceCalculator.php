<?php

namespace App\Domain\Calculators;

class PriceCalculator
{
    public function calculateTotalPrice($product, $days, $selectedServices)
    {
        $price = $product['PRICE'];
        $tariff = $product['TARIFF'];

        $productPrice = $this->calculateProductPrice($product, $days);
        $servicesPrice = $this->calculateServicesPrice($selectedServices, $days);

        return $productPrice + $servicesPrice;
    }

    private function calculateProductPrice($product, $days)
    {
        $productPrice = $this->getPricePerDay($product, $days);

        return $productPrice * $days;
    }

    public function calculateServicesPrice($selectedServices, $days)
    {
        $servicesPrice = 0;
        foreach ($selectedServices as $service) {
            $servicesPrice += (float)$service * $days;
        }
        return $servicesPrice;
    }
    public function getPricePerDay($product, $days)
    {
        $basePrice = $product['PRICE'];
        $tariffs = unserialize($product['TARIFF']);

        if (!is_array($tariffs)) {
            return $basePrice;
        }

        ksort($tariffs);

        $productPrice = $basePrice;

        foreach ($tariffs as $dayCount => $tariffPrice) {
            if ($days >= $dayCount) {
                $productPrice = $tariffPrice;
            }
        }

        return $productPrice;
    }
}