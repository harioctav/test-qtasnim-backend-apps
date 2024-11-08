<?php

namespace App\Services\Product;

use LaravelEasyRepository\ServiceApi;
use App\Repositories\Product\ProductRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;

class ProductServiceImplement extends ServiceApi implements ProductService
{
  /**
   * set title message api for CRUD
   * @param string $title
   */
  protected string $title = "";
  /**
   * uncomment this to override the default message
   * protected string $create_message = "";
   * protected string $update_message = "";
   * protected string $delete_message = "";
   */

  /**
   * don't change $this->mainRepository variable name
   * because used in extends service class
   */
  protected ProductRepository $mainRepository;

  public function __construct(ProductRepository $mainRepository)
  {
    $this->mainRepository = $mainRepository;
  }

  public function query()
  {
    return $this->mainRepository->query();
  }

  public function getWhere(
    $wheres = [],
    $columns = '*',
    $comparisons = '=',
    $orderBy = null,
    $orderByType = null
  ) {
    return $this->mainRepository->getWhere(
      wheres: $wheres,
      columns: $columns,
      comparisons: $comparisons,
      orderBy: $orderBy,
      orderByType: $orderByType
    );
  }

  public function handleStoreData($request)
  {
    try {
      $payload = $request->validated();
      return $this->mainRepository->create($payload);
    } catch (\Exception $e) {
      DB::rollBack();
      Log::info($e->getMessage());
      throw new InvalidArgumentException('Adding Error. Please Check the Logs!');
    }
  }
}
