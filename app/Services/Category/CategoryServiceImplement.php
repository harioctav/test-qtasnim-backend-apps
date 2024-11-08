<?php

namespace App\Services\Category;

use LaravelEasyRepository\ServiceApi;
use App\Repositories\Category\CategoryRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;

class CategoryServiceImplement extends ServiceApi implements CategoryService
{
  /**
   * set title message api for CRUD
   * @param string $title
   */
  protected string $title = "Categories Management";

  protected string $create_message = "Successfully Created Data";
  protected string $update_message = "Successfully Updated Data";
  protected string $delete_message = "Successfully Deleted Data";

  /**
   * don't change $this->mainRepository variable name
   * because used in extends service class
   */
  protected CategoryRepository $mainRepository;

  public function __construct(CategoryRepository $mainRepository)
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

  public function handleUpdateData($category, $request)
  {
    try {
      $payload = $request->validated();
      return $this->mainRepository->update($category->id, $payload);
    } catch (\Exception $e) {
      DB::rollBack();
      Log::info($e->getMessage());
      throw new InvalidArgumentException('Adding Error. Please Check the Logs!');
    }
  }
}
