<?php

namespace App\Logging;

use Monolog\Formatter\LineFormatter;
use Monolog\LogRecord;

class FilteredLineFormatter extends LineFormatter
{
    protected const FILTERED_REPLACEMENT = "[Filtered]";
    protected const CONFIG_KEYS_TO_FILTER = [
        'database.connections.mysql.password',
        'services.whatsapp.from-phone-number-id',
        'services.whatsapp.token',
    ];

    public function format(LogRecord $record): string
    {
        $log = parent::format($record);

        foreach (self::CONFIG_KEYS_TO_FILTER as $key) {
            $log = str_replace(config($key), self::FILTERED_REPLACEMENT, $log);
        }

        return $log;
    }
}