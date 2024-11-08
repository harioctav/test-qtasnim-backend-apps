<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
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
  public function index()
  {
    return response()->json([
      'products' => $this->productService->query()->latest()->paginate(5)
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
