<?php

namespace App\Logging;

class CustomLogsFormatter
{
    public function __invoke($logger)
    {
        foreach ($logger->getHandlers() as $handler) {
            $handler->setFormatter(
                tap(
                    new FilteredLineFormatter(null, 'Y-m-d H:i:s', true, true),
                    function ($formatter) {
                        $formatter->includeStacktraces();
                    }
                )
            );
        }
    }
}