<?php

namespace FlintWebmedia\FlintboardAffiliate\app\Helpers;

use Illuminate\Support\Facades\DB;

class ProductHelper
{
    public function __construct()
    {

    }

    /**
     * This function returns all unique custom values, for example all brands
     *
     * @param $attribute (string, case-insensitive) name of custom attribute
     * @return mixed returns all unique values in a Collection, otherwise return false
     */
    public function getUniqueValues($attribute)
    {
        $this->attr = $attribute;

        // Get all values of given attribute, make distinct and return just 'value' columns
        $values = DB::table('values')
            ->join('attributes', function ($join) {
                $join->on('attributes.id', '=', 'values.attribute_id')
                    ->where('attributes.name', '=', $this->attr);
            })
            ->distinct()
            ->get(['value']);

        // Return result
        if ($values) {
            return $values;
        }

        return false;
    }

    public function getCountCustomValues($attribute, $value)
    {
        $this->attr = $attribute;

        $values = DB::table('values')
            ->join('attributes', function ($join) {
                $join->on('attributes.id', '=', 'values.attribute_id')
                    ->where('attributes.name', '=', $this->attr);
            })
            ->where('value', $value)
            ->count();

        if ($values) {
            return $values;
        }

        return false;
    }
}