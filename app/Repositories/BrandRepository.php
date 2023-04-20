<?php

namespace App\Repositories;

use App\Models\Brand;

class BrandRepository
{
    public static function saveImport(array $row): Brand
    {
        $name = trim($row[3]);

        $query = Brand::where('name', $name);

        if ($query->exists()) {
            $brand = $query->first();
        } else {
            $brand = Brand::create(['name' => $name]);
        }

        return $brand;
    }
}
