<?php

namespace App\Twig;

use Doctrine\Common\Collections\Collection;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('unique', [$this, 'uniqueFilter']),
        ];
    }

    public function uniqueFilter($items): array
    {
        // Si c'est une collection Doctrine, la convertir en tableau
        if ($items instanceof Collection) {
            $items = $items->toArray();
        }

        return array_unique($items, SORT_REGULAR);
    }
}
