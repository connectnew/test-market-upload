<?php

namespace App\Repositories;

use App\Models\Brand;

class BrandRepository
{
    const INDEX_BRAND_NAME = 3;

    public static function saveImport(array $row): Brand
    {
        $name = trim($row[self::INDEX_BRAND_NAME]);

        $query = Brand::where('name', $name);

        if ($query->exists()) {
            $brand = $query->first();
        } else {
            $brand = Brand::create(['name' => $name]);
        }

        return $brand;
    }
}
