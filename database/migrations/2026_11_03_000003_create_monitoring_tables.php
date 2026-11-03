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
        if (! Schema::hasTable('ews')) {
            Schema::create('ews', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('pasar_id')->nullable();
                $table->integer('bahan_id')->nullable();
                $table->string('acuan')->nullable();
                $table->string('batas')->nullable();
                $table->integer('h1')->nullable();
                $table->integer('h2')->nullable();
                $table->integer('h3')->nullable();
                $table->integer('h4')->nullable();
                $table->string('kesimpulan')->nullable();
                $table->timestamps();
                $table->date('tanggal')->nullable();
            });
        }

        if (! Schema::hasTable('kenaikan')) {
            Schema::create('kenaikan', function (Blueprint $table) {
                $table->increments('id');
                $table->string('tanggal')->nullable();
                $table->integer('bahan_id')->nullable();
                $table->integer('pasar_id')->nullable();
                $table->string('acuan')->nullable();
                $table->string('harga')->nullable();
                $table->string('kenaikan')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('stok')) {
            Schema::create('stok', function (Blueprint $table) {
                $table->increments('id');
                $table->date('tanggal')->nullable();
                $table->unsignedInteger('stok')->nullable();
                $table->timestamps();
                $table->unsignedInteger('bahan_id')->nullable();
                $table->unsignedInteger('pasar_id')->nullable();

                $table->foreign('bahan_id')->references('id')->on('bahan')->cascadeOnUpdate();
                $table->foreign('pasar_id')->references('id')->on('pasar')->cascadeOnUpdate();
            });
        }

        if (! Schema::hasTable('stok_kota')) {
            Schema::create('stok_kota', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('bulan')->nullable();
                $table->integer('tahun')->nullable();
                $table->integer('minggu_1')->default(0)->nullable();
                $table->integer('minggu_2')->default(0)->nullable();
                $table->integer('minggu_3')->default(0)->nullable();
                $table->integer('minggu_4')->default(0)->nullable();
                $table->integer('minggu_5')->default(0)->nullable();
                $table->timestamps();
                $table->unsignedInteger('bahan_id')->nullable();
                $table->integer('pasar_id')->nullable();

                $table->foreign('bahan_id')->references('id')->on('bahan')->cascadeOnDelete()->cascadeOnUpdate();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Safe no-op to prevent accidental table drops
    }
};
