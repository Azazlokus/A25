<?php

namespace App\Infrastructure;

class CurrencyExchanger
{
    private $apiUrl = 'https://www.cbr-xml-daily.ru/daily_json.js';

    public function convertRubTo($amountRub, $currencyCode)
    {
        $json = file_get_contents($this->apiUrl);
        $data = json_decode($json, true);

        if (isset($data['Valute'][$currencyCode])) {
            $rateCny = $data['Valute'][$currencyCode]['Value'];

            return round($amountRub / $rateCny, 2);
        }

        return false;
    }
}