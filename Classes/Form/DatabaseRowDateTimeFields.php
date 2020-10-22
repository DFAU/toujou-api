<?php

declare(strict_types=1);

namespace DFAU\ToujouApi\Form;

use TYPO3\CMS\Backend\Form\FormDataProviderInterface;
use TYPO3\CMS\Core\Database\Query\QueryHelper;

/**
 * Migrate date and datetime db field values to timestamp
 */
class DatabaseRowDateTimeFields implements FormDataProviderInterface
{
    /**
     * Migrate date and datetime db field values to timestamp
     *
     * @param array $result
     * @return array
     */
    public function addData(array $result)
    {
        $dateTimeTypes = QueryHelper::getDateTimeTypes();
        $dateTimeFormats = QueryHelper::getDateTimeFormats();

        foreach ($result['processedTca']['columns'] as $column => $columnConfig) {
            if (isset($columnConfig['config']['dbType'])
                && in_array($columnConfig['config']['dbType'], $dateTimeTypes, true)
            ) {
                if (!empty($result['databaseRow'][$column])
                    && $result['databaseRow'][$column] !== $dateTimeFormats[$columnConfig['config']['dbType']]['empty']
                ) {
                    $result['databaseRow'][$column] = date($dateTimeFormats[$columnConfig['config']['dbType']]['format'], strtotime($result['databaseRow'][$column] . ' UTC'));
                } else {
                    $result['databaseRow'][$column] = null;
                }
            }
        }
        return $result;
    }
}
