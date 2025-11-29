<?php

namespace App\Domain\Enums;

enum VehicleType: string
{
    case CAR = 'carro';
    case MOTO = 'moto';
    case TRUCK = 'caminhao';

    public function hourlyRate(): float
    {
        return match ($this) {
            self::CAR   => 5.0,
            self::MOTO  => 3.0,
            self::TRUCK => 10.0,
        };
    }
}
