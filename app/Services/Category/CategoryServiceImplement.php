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
  protected CategoryRepository $mainRepository;

  public function __construct(CategoryRepository $mainRepository)
  {
    $this->mainRepository = $mainRepository;
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
