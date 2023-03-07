<?php

namespace App\Utils;

use App\Entity\Wish;

class Censurator
{
    const BAN_WORDS = ['michel', 'arthur'];
    public function purify(string $text){

        return str_ireplace(self::BAN_WORDS, "*****", $text);
       }
}