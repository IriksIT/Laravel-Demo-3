<?php

declare(strict_types=1);

namespace App\Orchid\Layouts\Dashboard;

use Orchid\Screen\Layouts\Chart;

class CategoryChart extends Chart
{
    protected $target = 'categoryChart';

    protected $title = 'Weergaven per categorie';

    protected $type = self::TYPE_PIE;

    protected $height = 300;
}
