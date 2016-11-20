<?php

namespace Rhinodontypicus\EloquentSpreadsheets;

use Google_Client;
use Google_Service_Sheets;

class SpreadsheetClient
{
    /**
     * @var
     */
    private $credentialsPath;

    /**
     * @var Google_Client
     */
    private $client;

    /**
     * SpreadsheetClient constructor.
     * @param $credentialsPath
     */
    public function __construct($credentialsPath)
    {
        $this->credentialsPath = $credentialsPath;
        $this->initClient();
    }

    /**
     * Init Client.
     */
    private function initClient()
    {
        putenv('GOOGLE_APPLICATION_CREDENTIALS='.$this->credentialsPath);
        $this->client = new Google_Client();
        $this->client->useApplicationDefaultCredentials();
        $this->client->addScope(Google_Service_Sheets::SPREADSHEETS);
    }

    /**
     * @return Google_Client
     */
    public function client()
    {
        return $this->client;
    }
}
