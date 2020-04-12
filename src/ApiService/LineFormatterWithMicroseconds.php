<?php
declare(strict_types=1);

namespace App\ApiService;

use Monolog\Formatter\LineFormatter;

class LineFormatterWithMicroseconds extends LineFormatter
{
    const SIMPLE_DATE = "Y-m-d H:i:s.u";
}