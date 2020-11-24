<?php

namespace App\Http\Controllers\Api\V1\Traits;

use App\Models\Category;
use App\Models\Brand;
use App\Models\Product;

trait ImportTrait
{
    protected function saveCategory(array $row): Category
    {
        $nameRoot = trim($row[0]);
        $nameSecond = trim($row[1]);
        $nameThird = trim($row[2]);

        $queryRoot = Category::where('name', $nameRoot);
        $querySecond = Category::where('name', $nameSecond);
        $queryThird = Category::where('name', $nameThird);

        if ($queryRoot->exists()) {
            $root = $queryRoot->first();
        } else {
            $root = Category::create(['name' => $nameRoot]);
        }

        if ($querySecond->exists()) {
            $second = $querySecond->first();
        } else {
            $second = Category::create(['name' => $nameSecond]);
            if ($root) {
                $second->prependToNode($root)->save();
            }
        }

        if ($queryThird->exists()) {
            $third = $queryThird->first();
        } else {
            $third = Category::create(['name' => $nameThird]);
            if ($second) {
                $third->prependToNode($second)->save();
            }
        }

        return $third;
    }

    protected function saveBrand(array $row): Brand
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

    protected function saveProduct(array $row, Category $category, Brand $brand): Product
    {
        $code = trim($row[5]);

        $row[4] = trim($row[4]);
        $row[8] = trim(mb_strtolower($row[8]));
        $row[9] = trim(mb_strtolower($row[9]));

        $query = Product::where('code', $code);

        if ($query->exists()) {
            $product = $query->first();
            $product->fill([
                'name' => $row[4],
                'description' => $row[6],
                'price' => $row[7],
                'warranty' => $row[8] == 'нет' ? null : $row[8],
                'stock' => $row[9] == 'есть в наличие' ? 1 : 0,
            ]);
            $product->save();
        } else {
            $product = Product::create([
                'category_id' => $category->id,
                'brand_id' => $brand->id,
                'name' => $row[4],
                'code' => $code,
                'description' => $row[6],
                'price' => $row[7],
                'warranty' => $row[8] == 'нет' ? null : $row[8],
                'stock' => $row[9] == 'есть в наличие' ? 1 : 0,
            ]);
        }

        return $product;
    }
}
