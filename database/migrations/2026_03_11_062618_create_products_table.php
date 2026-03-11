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
            $table->string('name');
            $table->string('image_main'); // Hình chính
            $table->json('images_sub')->nullable(); // Nhiều hình phụ (Mảng)
            $table->text('description')->nullable(); // Chi tiết
            $table->json('specs')->nullable(); // Thông số kỹ thuật (Dạng Key-Value)
            $table->decimal('price', 15, 2);
            $table->string('category'); // Loại
            $table->string('document')->nullable(); // Link tài liệu (PDF/Doc)
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
