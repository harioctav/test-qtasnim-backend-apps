<?php

namespace App\Repositories\Product;

use LaravelEasyRepository\Implementations\Eloquent;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;

class ProductRepositoryImplement extends Eloquent implements ProductRepository
{
  protected Product $model;

  public function __construct(Product $model)
  {
    $this->model = $model;
  }

  /**
   * Get a query builder instance for the model.
   *
   * @return \Illuminate\Database\Eloquent\Builder
   */
  public function query(): Builder
  {
    return $this->model->query();
  }

  public function getWhere(
    $wheres = [],
    $columns = '*',
    $comparisons = '=',
    $orderBy = null,
    $orderByType = null
  ) {
    $data = $this->model->select($columns);

    if (!empty($wheres)) {
      foreach ($wheres as $key => $value) {
        if (is_array($value)) {
          $data = $data->whereIn($key, $value);
        } else {
          $data = $data->where($key, $comparisons, $value);
        }
      }
    }

    if ($orderBy) {
      $data = $data->orderBy($orderBy, $orderByType);
    }

    return $data;
  }
}
