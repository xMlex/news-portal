<?php
/**
 * Created by Maxim Novikov
 * Date: 20.10.17 8:45
 */

namespace app\helpers;


use yii\db\ActiveQuery;

class QueryHelper
{
    /**
     * Добавить фильтр по дате, расположенной в диапазоне
     * @param ActiveQuery $query
     * @param string $property поле, приходящее из DateRangePicker
     * @param string $filteredColumn собсно фильтруемый столбец
     * @param string $delimiter разделитель дат
     * @return ActiveQuery|boolean
     *
     */
    public static function addFilterBetweenDateRange($query, $property, $filteredColumn, $delimiter = ' - ')
    {
        if (is_object($query) && $query instanceof ActiveQuery) {
            $dateRange = explode($delimiter, $property);
            if (is_array($dateRange) && count($dateRange) == 2) {
                $query->andFilterWhere(['between', $filteredColumn, $dateRange[0], $dateRange[1]]);
            }

            return $query;
        }

        return false;
    }
}