<?php

namespace Rhinodontypicus\EloquentSpreadsheets;

use Google_Client;
use Google_Service_Sheets;

class SpreadsheetService
{
    /**
     * @var Google_Client
     */
    private $client;

    /**
     * @var Google_Service_Sheets
     */
    private $service;

    /**
     * SpreadsheetService constructor.
     * @param Google_Client $client
     */
    public function __construct(Google_Client $client)
    {
        $this->client = $client;
        $this->service = new Google_Service_Sheets($client);
    }

    /**
     * @return Google_Service_Sheets
     */
    public function service()
    {
        return $this->service;
    }
}
