<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Services\Category\CategoryService;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
  protected $categoryService;

  /**
   * Create a new controller instance.
   *
   * @return void
   */
  public function __construct(
    CategoryService $categoryService,
  ) {
    $this->categoryService = $categoryService;
  }

  /**
   * Display a listing of the resource.
   */
  public function index()
  {
    return response()->json([
      'categories' => $this->categoryService->query()->latest()->paginate(5)
    ]);
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(CategoryRequest $request)
  {
    $category = $this->categoryService->handleStoreData($request);
    return response()->json([
      'category' => $category
    ]);
  }

  /**
   * Display the specified resource.
   */
  public function show(Category $category)
  {
    return response()->json([
      'category' => $category
    ]);
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(CategoryRequest $request, Category $category)
  {
    $category->update($request->validated());

    return response()->json([
      'category' => $category
    ]);
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy(Category $category)
  {
    $this->categoryService->delete($category->id);
    return response()->json([
      'message' => 'Successfully Deleted Data Category'
    ]);
  }
}
