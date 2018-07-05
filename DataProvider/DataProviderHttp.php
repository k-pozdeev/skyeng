<?php

namespace DataProvider;

use DataProvider\Exception\InvalidRequestException;
use DataProvider\Exception\ResponseException;

class DataProviderHttp implements DataProviderInterface
{
    public function get(array $request): array
    {
        throw new ResponseException("Not implemented");
    }
}