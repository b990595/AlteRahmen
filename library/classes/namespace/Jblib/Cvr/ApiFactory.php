<?php

namespace Jblib\Cvr;

use Zend\Cache\StorageFactory;
use Zend\Http\Client;

class ApiFactory {

    /**
     * Create new CVR API client
     * 
     * @param ServiceManager $serviceManager
     * @return \Jblib\Cvr\Api
     * @throws Exception\ConfigException
     */
    public static function factory() {
        $config = array(
            'jbcvr' => array(
                'client' => array(
                    'uri' => 'https://cvrapi.dk/api',
                    'params' => array(
                        'adapter' => 'Zend\Http\Client\Adapter\Curl',
                        'sslverifypeer' => true,
                        'useragent' => 'CVR API - Portal - Jakob M. Nielsen +45 96 57 51 43',
                        'proxyhost' => 'proxy-nord.sdc.dk',
                        'proxyport' => 80,
                    ),
                ),
                'storage' => array(
                    'adapter' => array(
                        'name' => 'apc',
                        'options' => array('ttl' => 86400 * 7, 'namespace' => 'JblibCvr'),
                    ),
                )
            )
        );

        if (!isset($config['jbcvr']['client']['uri']) || !is_string($config['jbcvr']['client']['uri'])) {
            throw Exception\ConfigException::missingClientConfig();
        }

        $clientParams = isset($config['jbcvr']['client']['params']) && is_array($config['jbcvr']['client']['params']) ? $config['jbcvr']['client']['params'] : array();

        if (!isset($config['jbcvr']['storage']) || !is_array($config['jbcvr']['storage'])) {
            throw Exception\ConfigException::missingStorageConfig();
        }

        $storage = StorageFactory::factory($config['jbcvr']['storage']);
        $client = new Client($config['jbcvr']['client']['uri'], $clientParams);

        return new Api($storage, $client);
    }

}
