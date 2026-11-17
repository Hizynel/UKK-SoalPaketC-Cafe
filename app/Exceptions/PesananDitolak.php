<?php

namespace App\Exceptions;

class PesananDitolak extends \RuntimeException
{
    public function __construct(string $message, public array $daftar = [])
    {
        parent::__construct($message);
    }
}