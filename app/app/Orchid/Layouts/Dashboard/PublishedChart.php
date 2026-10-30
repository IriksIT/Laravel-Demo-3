<?php

declare(strict_types=1);

namespace App\Orchid\Layouts\Dashboard;

use Orchid\Screen\Layouts\Chart;

class PublishedChart extends Chart
{
    protected $target = 'publishedChart';

    protected $title = 'Gepubliceerde berichten per maand';

    protected $type = self::TYPE_BAR;

    protected $height = 300;
}
