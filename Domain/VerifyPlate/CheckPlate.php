<?php

namespace App\Domain\VerifyPlate;

final class CheckPlate
{
    public static function normalize(string $raw): string
    {
        $plate = strtoupper(trim($raw));

        if (!preg_match('/^[A-Z]{3}[0-9][A-Z0-9][0-9]{2}$/', $plate)) {
            throw new \InvalidArgumentException(
                "Placa inválida. Use o formato ABC1234 ou ABC1D23."
            );
        }

        return $plate;
    }
}
