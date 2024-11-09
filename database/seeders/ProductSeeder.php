<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    $json = File::get(public_path('products.json'));

    $decode = json_decode($json, true);
    $chunks = array_chunk($decode, 1000);

    // Insert to Database
    foreach ($chunks as $chunk) {
      foreach ($chunk as &$item) {
        $item['uuid'] = (string) Str::uuid();
        $item['created_at'] = now();
        $item['updated_at'] = now();
      }

      // Save to database
      Product::insert($chunk);
    }
  }
}
