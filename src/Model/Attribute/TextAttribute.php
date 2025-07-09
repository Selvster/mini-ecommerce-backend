<?php

namespace App\Model\Attribute;

class TextAttribute extends AbstractAttribute
{
    public function __construct()
    {
        $this->type = 'text';
    }

}