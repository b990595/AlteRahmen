<?php

namespace Jblib\Cvr\Exception;

class ConfigException extends \RuntimeException
{
    /**
     * Missing client config
     *
     * @return \self
     */
    public static function missingClientConfig()
    {
        return new self("[jbcvr][client] not found in config.");
    }
    
    /**
     * Missing storage config
     *
     * @return \self
     */
    public static function missingStorageConfig()
    {
        return new self("[jbcvr][storage] not found in config.");
    }
}
