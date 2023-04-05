<?php

namespace App\Library\Utils;

class ArrayHelper
{

    /**
     * @param $array
     * @param $field
     * @return array
     */
    public static function array_column($input, string $column)
    {
        $result = [];
        foreach ($input as $arr) {
            if (is_array($arr)) {
                if (!isset($arr[$column])) {
                    throw new \Exception("array_colonm column 找不到");
                }
                $result[] = $arr[$column];
            }

            if(is_object($arr)) {
                if (!isset($arr[$column])) {
                    throw new \Exception("array_colonm column 找不到");
                }
                $result[] = $arr->{$column};
            }
        }

        return $result;
    }


    public static function array_index($input, $column_index_key = '')
    {
        $result = [];
        foreach ($input as $arr) {
            if (is_array($arr)) {
                if (!isset($arr[$column_index_key])) {
                    throw new \Exception("column_index_key 找不到");
                }
                $result[$arr[$column_index_key]] = $arr;
            }

            if(is_object($arr)) {
                if (!isset($arr[$column_index_key])) {
                    throw new \Exception("column_index_key 找不到");
                }
                $result[$arr[$column_index_key]] = $arr->toArray();
            }
        }

        return $result;
    }

}
