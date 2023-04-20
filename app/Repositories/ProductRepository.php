<?php

namespace App\Repositories;

use App\Models\Brand;
use App\Models\Product;
use App\Models\Category;
use App\Helpers\StrHelper;

class ProductRepository
{
    const IMPORT_NAME_ROW = 4;
    const IMPORT_CODE_ROW = 5;
    const IMPORT_DESC_ROW = 6;
    const IMPORT_PRICE_ROW = 7;
    const IMPORT_WARRANTY_ROW = 8;
    const IMPORT_STOCK_ROW = 9;

    public static function saveImport(array $row, Category $category, Brand $brand): Product
    {
        $code = trim($row[self::IMPORT_CODE_ROW]);

        $row[self::IMPORT_NAME_ROW] = trim($row[self::IMPORT_NAME_ROW]);
        $row[self::IMPORT_WARRANTY_ROW] = StrHelper::trimLower($row[self::IMPORT_WARRANTY_ROW]);
        $row[self::IMPORT_STOCK_ROW] = StrHelper::trimLower($row[self::IMPORT_STOCK_ROW]);

        $query = Product::where('code', $code);

        if ($query->exists()) {
            $product = $query->first();
            $product->fill([
                'name' => $row[self::IMPORT_NAME_ROW],
                'description' => $row[self::IMPORT_DESC_ROW],
                'price' => $row[self::IMPORT_PRICE_ROW],
                'warranty' => $row[self::IMPORT_WARRANTY_ROW] == 'нет' ? null : $row[self::IMPORT_WARRANTY_ROW],
                'stock' => $row[self::IMPORT_STOCK_ROW] == 'есть в наличие' ? 1 : 0,
            ]);
            $product->save();
        } else {
            $product = Product::create([
                'category_id' => $category->id,
                'brand_id' => $brand->id,
                'name' => $row[self::IMPORT_NAME_ROW],
                'code' => $code,
                'description' => $row[self::IMPORT_DESC_ROW],
                'price' => $row[self::IMPORT_PRICE_ROW],
                'warranty' => $row[self::IMPORT_WARRANTY_ROW] == 'нет' ? null : $row[self::IMPORT_WARRANTY_ROW],
                'stock' => $row[self::IMPORT_STOCK_ROW] == 'есть в наличие' ? 1 : 0,
            ]);
        }

        return $product;
    }
}
