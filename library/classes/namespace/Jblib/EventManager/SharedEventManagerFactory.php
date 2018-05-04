<?php

namespace Jblib\EventManager;

use Zend\EventManager\SharedEventManager;

/**
 * @author jmn
 */
class SharedEventManagerFactory
{

    /**
     * @var SharedEventManager
     */
    private static $instance;

    /**
     * @return SharedEventManager
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new SharedEventManager();
        }

        return self::$instance;
    }
}
