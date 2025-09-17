<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class RemoveDuplicateProducts extends Command
{
  protected $signature = 'products:remove-duplicates';
  protected $description = 'Remove duplicate products from the database';

  public function handle()
  {
    $this->info('Checking for duplicate products...');

    // Find duplicates based on name
    $duplicates = Product::select('name', DB::raw('COUNT(*) as count'))
      ->groupBy('name')
      ->having('count', '>', 1)
      ->get();

    if ($duplicates->isEmpty()) {
      $this->info('No duplicate products found.');
      return 0;
    }

    $this->info('Found duplicates for: ' . $duplicates->pluck('name')->implode(', '));

    foreach ($duplicates as $duplicate) {
      $products = Product::where('name', $duplicate->name)
        ->orderBy('id')
        ->get();

      // Keep the first one, delete the rest
      $keepProduct = $products->first();
      $deleteProducts = $products->skip(1);

      $this->info("Keeping product ID {$keepProduct->id}: {$keepProduct->name}");

      foreach ($deleteProducts as $product) {
        $this->warn("Deleting duplicate ID {$product->id}: {$product->name}");
        $product->delete();
      }
    }

    $this->info('Duplicate products removed successfully!');
    return 0;
  }
}
