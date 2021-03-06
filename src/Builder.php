<?php

namespace G4\DataMapper;

use G4\DataMapper\Common\Bulk;
use G4\DataMapper\Common\CollectionNameInterface;
use G4\DataMapper\Engine\Elasticsearch\ElasticsearchAdapter;
use G4\DataMapper\Engine\Elasticsearch\ElasticsearchClientFactory;
use G4\DataMapper\Engine\Elasticsearch\ElasticsearchMapper;
use G4\DataMapper\Engine\MySQL\MySQLClientFactory;
use G4\DataMapper\Common\AdapterInterface;
use G4\DataMapper\Common\MapperInterface;
use G4\DataMapper\Engine\MySQL\MySQLAdapter;
use G4\DataMapper\Engine\MySQL\MySQLMapper;
use G4\DataMapper\Engine\MySQL\MySQLTableName;
use G4\DataMapper\Engine\MySQL\MySQLTransaction;
use G4\DataMapper\Engine\Solr\SolrAdapter;
use G4\DataMapper\Engine\Solr\SolrClientFactory;
use G4\DataMapper\Engine\Solr\SolrMapper;

class Builder
{

    /**
     * @var AdapterInterface
     */
    private $adapter;

    /**
     * @var CollectionNameInterface
     */
    private $collectionName;

    /**
     * @return Builder
     */
    public static function create()
    {
        return new static();
    }

    /**
     * @param AdapterInterface $adapter
     * @return Builder
     */
    public function adapter(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
        return $this;
    }

    /**
     * @param array $params
     * @return Builder
     */
    public function engineMySQL(array $params)
    {
        $this->adapter = new MySQLAdapter(new MySQLClientFactory($params));
        return $this;
    }

    /**
     * @param array $params
     * @return Builder
     */
    public function engineSolr(array $params)
    {
        $this->adapter = new SolrAdapter(new SolrClientFactory($params));
        return $this;
    }

    public function engineElasticsearch(array $params)
    {
        $this->adapter = new ElasticsearchAdapter(new ElasticsearchClientFactory($params));
        return $this;
    }

    /**
     * @return MapperInterface
     * @throws \Exception
     */
    public function buildMapper()
    {
        $this->validateDependencies();
        return $this->strategy();
    }

    /**
     * @return Bulk
     * @throws \Exception
     */
    public function buildBulk()
    {
        $this->validateDependencies();
        return new Bulk($this->adapter, $this->collectionName);
    }

    //TODO: Drasko - change this!!!
    public function buildTransaction()
    {
        $this->validateDependencies();
        return new MySQLTransaction($this->adapter);
    }

    /**
     * @param CollectionNameInterface $collectinName
     * @return Builder
     */
    public function collectionName(CollectionNameInterface $collectionName)
    {
        $this->collectionName = $collectionName;
        return $this;
    }

    /**
     * @throws \Exception
     * @return MapperInterface
     */
    private function strategy()
    {
        switch (true) {
            case $this->adapter instanceof MySQLAdapter:
                $mapper = new MySQLMapper($this->adapter, $this->collectionName);
                break;
            case $this->adapter instanceof SolrAdapter:
                $mapper = new SolrMapper($this->collectionName, $this->adapter);
                break;
            case $this->adapter instanceof ElasticsearchAdapter:
                $mapper = new ElasticsearchMapper($this->collectionName, $this->adapter);
                break;
            default:
                throw new \Exception('Unknown engine', 601);
        }
        return $mapper;
    }

    private function validateDependencies()
    {
        if (!$this->adapter instanceof AdapterInterface) {
            throw new \Exception('Adapter instance must implement AdapterInterface', 601);
        }

        if (!$this->collectionName instanceof CollectionNameInterface) {
            throw new \Exception('DataSet cannot be emty', 601);
        }
    }
}
