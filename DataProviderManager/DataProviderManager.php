<?php

namespace DataProviderManager;

use DataProvider\DataProviderInterface;
use DataProvider\Exception\InvalidRequestException;
use DataProvider\Exception\ResponseException;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Cache\InvalidArgumentException;
use Psr\Log\LoggerInterface;

class DataProviderManager implements DataProviderInterface
{
    private $dataProvider;
    private $cache;
    private $logger;

    /**
     * @param DataProviderInterface $dataProvider
     * @param CacheItemPoolInterface $cache
     * @param LoggerInterface $logger
     */
    public function __construct(DataProviderInterface $dataProvider, CacheItemPoolInterface $cache, LoggerInterface $logger)
    {
        $this->dataProvider = $dataProvider;
        $this->cache = $cache;
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function get(array $request): array
    {
        $cacheKey = $this->getCacheKey($request);
        if ($cacheKey === false) {
            $this->logger->critical("Invalid request data", ['data' => $request]);
            throw new InvalidRequestException("Request data is not correctly json-serializable");
        }

        try {
            $cacheItem = $this->cache->getItem($cacheKey);
        }
        catch (InvalidArgumentException $e) {
            $this->logger->critical("Exception thrown by cache", [
                'code' => $e->getCode(),
                'message' => $e->getMessage()
            ]);
            throw new InvalidRequestException("Cache treated request data as invalid");
        }

        if ($cacheItem->isHit()) {
            return $cacheItem->get();
        }

        try {
            $result = $this->dataProvider->get($request);
            $cacheItem
                ->set($result)
                ->expiresAt(
                    (new \DateTime())->modify('+1 day')
                );
        }
        catch (InvalidRequestException $e) {
            $this->logger->critical("Invalid request", [
                'code' => $e->getCode(),
                'message' => $e->getMessage()
            ]);
            throw $e;
        }
        catch (ResponseException $e) {
            $this->logger->critical("Response exception", [
                'code' => $e->getCode(),
                'message' => $e->getMessage()
            ]);
            throw $e;
        }

        return $result;
    }

    /**
     * @param array $input
     * @return string|bool
     */
    private function getCacheKey(array $input)
    {
        return json_encode($input);
    }
}