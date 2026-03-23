<?php

namespace App\Domain\Recipe\Enums;

enum RecipeType: string
{
    case Starter = 'starter';
    case Main = 'main';
    case Dessert = 'dessert';
    case Cocktail = 'cocktail';
}
