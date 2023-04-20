<?php

namespace App\Repositories;

use App\Models\Category;

class CategoryRepository
{
    const IMPORT_FIRST_CATEGORY_ROW = 0;
    const IMPORT_SECOND_CATEGORY_ROW = 1;
    const IMPORT_THIRD_CATEGORY_ROW = 2;

    public static function saveImport(array $row): Category
    {
        $nameRoot = trim($row[self::IMPORT_FIRST_CATEGORY_ROW]);
        $nameSecond = trim($row[self::IMPORT_SECOND_CATEGORY_ROW]);
        $nameThird = trim($row[self::IMPORT_THIRD_CATEGORY_ROW]);

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
}
