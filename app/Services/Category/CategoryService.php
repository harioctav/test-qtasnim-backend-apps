<?php

namespace App\Services\Category;

use App\Models\Category;
use LaravelEasyRepository\BaseService;

interface CategoryService extends BaseService
{
  public function handleStoreData($request);
  public function handleUpdateData(Category $category, $request);
}
