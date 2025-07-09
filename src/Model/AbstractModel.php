<?php

namespace App\Model;

abstract class AbstractModel
{
    protected ?int $id = null; 

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }
}