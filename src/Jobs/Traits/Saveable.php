<?php

namespace Rhinodontypicus\EloquentSpreadsheets\Jobs\Traits;

use Rhinodontypicus\EloquentSpreadsheets\SpreadsheetService;

trait Saveable
{
    public $startColumn;
    public $endColumn;

    private function prepareConfig()
    {
        asort($this->config['sync_attributes']);
        $this->startColumn = array_first($this->config['sync_attributes']);
        $this->endColumn = array_last($this->config['sync_attributes']);
    }

    /**
     * @return array
     */
    public function getModelSyncedData()
    {
        $lettersToIdsTable = getLettersToIdsTable($this->startColumn, $this->endColumn);
        $result = [];

        foreach ($this->config['sync_attributes'] as $attributeKey => $attributeColumn) {
            if (empty($this->model->{$attributeKey})) {
                continue;
            }

            $result[$lettersToIdsTable[$attributeColumn]] = $this->model->{$attributeKey};
        }

        foreach (range(0, count(range($this->startColumn, $this->endColumn)) - 1) as $key) {
            if (!empty($result[$key])) {
                continue;
            }

            $result[$key] = '';
        }

        ksort($result);

        return $result;
    }

    /**
     * @param $rowToInsert
     */
    public function insertModelToSheet($rowToInsert)
    {
        $data = $this->getModelSyncedData();
        $batch = new \Google_Service_Sheets_BatchUpdateValuesRequest();
        $batch->setValueInputOption('RAW');

        $valueRange = new \Google_Service_Sheets_ValueRange();
        $valueRange->setValues([$data]);
        $valueRange->setRange("{$this->config['list_name']}!{$this->startColumn}{$rowToInsert}");
        $batch->setData($valueRange);

        app(SpreadsheetService::class)->service()->spreadsheets_values->batchUpdate(
            $this->config['spreadsheet_id'],
            $batch
        );
    }
}