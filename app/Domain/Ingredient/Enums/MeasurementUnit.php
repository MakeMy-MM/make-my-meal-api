<?php

namespace App\Domain\Ingredient\Enums;

enum MeasurementUnit: string
{
    case GRAM = 'g';
    case KILOGRAM = 'kg';
    case MILLILITER = 'ml';
    case LITER = 'l';
}
