<?php

namespace App\Model;

use Brick\Math\BigDecimal; 

class Price extends AbstractModel
{
    private BigDecimal $amount;
    private ?Currency $currency = null;

    public function getAmount(): BigDecimal 
    {
        return $this->amount;
    }

    public function setAmount(BigDecimal $amount): self
    {
        $this->amount = $amount;
        return $this;
    }

    public function getCurrency(): ?Currency
    {
        return $this->currency;
    }

    public function setCurrency(?Currency $currency): self
    {
        $this->currency = $currency;
        return $this;
    }
}