<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Services\Product\ProductService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
  protected $productService;

  /**
   * Create a new controller instance.
   *
   * @return void
   */
  public function __construct(
    ProductService $productService,
  ) {
    $this->productService = $productService;
  }

  /**
   * Display a listing of the resource.
   */
  public function index(Request $request)
  {
    $query = $this->productService->query();

    // Handle search
    if ($request->has('search')) {
      $searchTerm = $request->search;
      $query->where(function ($q) use ($searchTerm) {
        $q->where('name', 'LIKE', "%{$searchTerm}%")
          ->orWhere('stock', 'LIKE', "%{$searchTerm}%");
      });
    }

    // Handle sorting
    $sortField = $request->input('sort_field', 'created_at');
    $sortOrder = $request->input('sort_order', 'desc');

    $allowedSortFields = ['name', 'transaction_date', 'created_at'];

    if (in_array($sortField, $allowedSortFields)) {
      $query->orderBy($sortField, $sortOrder);
    }

    return response()->json([
      'products' => $query->paginate(5)
    ]);
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(ProductRequest $request)
  {
    $product = $this->productService->handleStoreData($request);
    return response()->json([
      'product' => $product
    ]);
  }

  /**
   * Display the specified resource.
   */
  public function show(Product $product)
  {
    return response()->json([
      'product' => $product
    ]);
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(ProductRequest $request, Product $product)
  {
    $product->update($request->validated());
    return response()->json([
      'product' => $product
    ]);
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy(Product $product)
  {
    $this->productService->delete($product->id);
    return response()->json([
      'message' => 'Successfully Deleted Data'
    ]);
  }
}
