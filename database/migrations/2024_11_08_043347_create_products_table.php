<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  /**
   * Run the migrations.
   */
  public function up(): void
  {
    Schema::create('products', function (Blueprint $table) {
      $table->id();
      $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
      $table->char('uuid')->index();
      $table->string('name', 100)->unique();
      $table->integer('stock');
      $table->integer('number_of_sales');
      $table->date('transaction_date');
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('products');
  }
};
