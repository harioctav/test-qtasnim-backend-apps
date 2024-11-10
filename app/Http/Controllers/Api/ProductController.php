<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Services\Product\ProductService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

    // Handle date range filter
    if ($request->has('start_date') && $request->has('end_date')) {
      $query->whereBetween('created_at', [
        $request->start_date . ' 00:00:00',
        $request->end_date . ' 23:59:59'
      ]);
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

  /**
   * Get sales comparison by product type
   */
  public function getSalesComparison(Request $request)
  {
    // Query untuk mendapatkan data penjualan berdasarkan type
    $salesByType = Product::select('type')
      ->selectRaw('COUNT(*) as total_products')
      ->selectRaw('SUM(number_of_sales) as total_sales')
      ->selectRaw('AVG(number_of_sales) as average_sales')
      ->selectRaw('MAX(number_of_sales) as highest_sales')
      ->selectRaw('MIN(number_of_sales) as lowest_sales')
      ->groupBy('type');

    // Handle filter date range jika ada
    if ($request->has('start_date') && $request->has('end_date')) {
      $salesByType->whereBetween('transaction_date', [
        $request->start_date . ' 00:00:00',
        $request->end_date . ' 23:59:59'
      ]);
    }

    // Get top selling products per type
    $topSellingProducts = Product::select('type', 'name', 'number_of_sales')
      ->whereIn('number_of_sales', function ($query) {
        $query->select(DB::raw('MAX(number_of_sales)'))
          ->from('products')
          ->groupBy('type');
      })
      ->get()
      ->groupBy('type');

    // Get lowest selling products per type
    $lowestSellingProducts = Product::select('type', 'name', 'number_of_sales')
      ->whereIn('number_of_sales', function ($query) {
        $query->select(DB::raw('MIN(number_of_sales)'))
          ->from('products')
          ->groupBy('type');
      })
      ->get()
      ->groupBy('type');

    $salesComparison = $salesByType->get()->map(function ($item) use ($topSellingProducts, $lowestSellingProducts) {
      return [
        'type' => $item->type,
        'total_products' => $item->total_products,
        'total_sales' => $item->total_sales,
        'average_sales' => round($item->average_sales, 2),
        'highest_sales' => [
          'value' => $item->highest_sales,
          'product' => $topSellingProducts[$item->type]->first()
        ],
        'lowest_sales' => [
          'value' => $item->lowest_sales,
          'product' => $lowestSellingProducts[$item->type]->first()
        ]
      ];
    });

    return response()->json([
      'sales_comparison' => $salesComparison
    ]);
  }

  /**
   * Get sales trend by product type
   */
  public function getSalesTrend(Request $request)
  {
    $query = Product::select(
      'type',
      DB::raw('DATE(transaction_date) as date'),
      DB::raw('SUM(number_of_sales) as daily_sales')
    )
      ->groupBy('type', DB::raw('DATE(transaction_date)'));

    if ($request->has('start_date') && $request->has('end_date')) {
      $query->whereBetween('transaction_date', [
        $request->start_date . ' 00:00:00',
        $request->end_date . ' 23:59:59'
      ]);
    }

    $salesTrend = $query->get()
      ->groupBy('type')
      ->map(function ($items) {
        return $items->map(function ($item) {
          return [
            'date' => $item->date,
            'sales' => $item->daily_sales
          ];
        });
      });

    return response()->json([
      'sales_trend' => $salesTrend
    ]);
  }
}
