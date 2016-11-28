<?php

namespace AppBundle\Entity;

class Theme
{
    const THEME_BLUE  = 1;
    const THEME_GREEN = 2;
    const THEME_RED   = 3;

    public static $THEMES = [self::THEME_BLUE, self::THEME_GREEN, self::THEME_RED];

    public static $THEMES_GPIO_NUMBERS = [
        self::THEME_BLUE  => 2,
        self::THEME_GREEN => 3,
        self::THEME_RED   => 4,
    ];

}

