<?php

namespace Jblib\Monolog\Formatter;

use Monolog\Formatter\LogstashFormatter as MonologLogstashFormatter;

class LogstashFormatter extends MonologLogstashFormatter
{

    public function __construct($applicationName, $systemName = null, $extraPrefix = '[extra]', $contextPrefix = '[context]', $version = self::V1)
    {
        parent::__construct($applicationName, $systemName, $extraPrefix, $contextPrefix, $version);
    }

    protected function formatV1(array $record)
    {
        if (empty($record['datetime'])) {
            $record['datetime'] = gmdate('c');
        }
        $message = array(
            '@timestamp' => $record['datetime'],
            '@version' => 1,
            'host' => $this->systemName,
        );
        if (isset($record['message'])) {
            $message['message'] = $record['message'];
        }
        if (isset($record['channel'])) {
            $message['type'] = $record['channel'];
            $message['channel'] = $record['channel'];
        }
        if (isset($record['level_name'])) {
            $message['level'] = $record['level_name'];
        }
        if ($this->applicationName) {
            $message['type'] = $this->applicationName;
        }
        if (!empty($record['extra'])) {
            $message['extra'] = $record['extra'];
        }
        if (!empty($record['context'])) {
            $message['context'] = $record['context'];
        }

        return $message;
    }

}