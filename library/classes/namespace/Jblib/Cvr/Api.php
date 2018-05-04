<?php

namespace Jblib\Cvr;

use Jblib\Cvr\CvrInfo\ProductionUnit;
use Zend\Cache\Storage\StorageInterface;
use Zend\Http\Client;
use Zend\Hydrator\ObjectProperty;

class Api
{

    /**
     * @var StorageInterface 
     */
    protected $storage;

    /**
     * @var Client 
     */
    protected $client;

    /**
     * @param StorageInterface $storage
     * @param Client $client
     */
    public function __construct(StorageInterface $storage, Client $client)
    {
        $this->storage = $storage;
        $this->client = $client;
    }

    /**
     * Get CVR info
     * 
     * @param int $cvr
     * @param string $country
     * @return CvrInfo
     */
    public function get($cvr, $country = 'dk')
    {
        $cvr = (int) $cvr;

        // Check and get in one operation, to avoid race with TTL
        $hasItem = false;
        $cvrInfo = null;
        $cvrArr = $this->storage->getItem($cvr, $hasItem);

        if (!$hasItem) {
            $this->client->setParameterGet(array(
                'vat' => $cvr,
                'country' => $country,
                'version' => 6,
            ));
            
            $response = $this->client->send();
            
            if ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300 && ($cvrArr = json_decode($response->getBody(), true))) {
                $hasItem = true;
                $this->storage->setItem($cvr, $cvrArr);
            }
        }

        if ($hasItem) {
            $hydrator = new ObjectProperty();
            $cvrInfo = $hydrator->hydrate($cvrArr, new CvrInfo());
            foreach ($cvrInfo->productionunits as $key => &$value) {
                $value = $hydrator->hydrate($value, new ProductionUnit());
            }
        }

        return $cvrInfo;
    }

}
