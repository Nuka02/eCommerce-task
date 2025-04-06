<?php

namespace App\Models;

class Price
{
    protected float $amount;
    protected string $currencyLabel;
    protected string $currencySymbol;

    public function __construct(array $data)
    {
        $this->amount = (float) $data['amount'];
        $this->currencyLabel = $data['currency_label'];
        $this->currencySymbol = $data['currency_symbol'];
    }

    public function toArray(): array
    {
        return [
            'amount' => $this->amount,
            'currency' => [
                'label' => $this->currencyLabel,
                'symbol' => $this->currencySymbol,
            ],
        ];
    }
}
