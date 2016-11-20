<?php

if (! function_exists('getLettersToIdsTable')) {
    /**
     * @param $startColumn
     * @param $endColumn
     * @return array
     */
    function getLettersToIdsTable($startColumn, $endColumn)
    {
        return array_combine(
            range($startColumn, $endColumn),
            range(0, count(range($startColumn, $endColumn)) - 1)
        );
    }
}
