<?php

namespace DataProvider;

use DataProvider\Exception\InvalidRequestException;
use DataProvider\Exception\ResponseException;

interface DataProviderInterface
{
    /**
     * @param array $request
     * @return array
     * @throws InvalidRequestException
     * @throws ResponseException
     */
    public function get(array $request): array;
}