<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class User extends Entity
{
    protected $dates = [];

    public function getClearedData(){
        unset($this->attributes['password']);
        return $this;
    }
}
