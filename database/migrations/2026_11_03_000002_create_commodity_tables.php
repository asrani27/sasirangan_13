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
        if (! Schema::hasTable('bahan')) {
            Schema::create('bahan', function (Blueprint $table) {
                $table->increments('id');
                $table->string('nama')->nullable();
                $table->unsignedInteger('satuan_id')->nullable();
                $table->unsignedInteger('kelompok_id')->nullable();
                $table->timestamps();
                $table->unsignedInteger('batas_harga')->nullable();
                $table->unsignedInteger('acuan')->nullable();
                $table->unsignedInteger('batas')->nullable();

                $table->foreign('satuan_id')->references('id')->on('satuan')->cascadeOnUpdate();
                $table->foreign('kelompok_id')->references('id')->on('kelompok')->cascadeOnUpdate();
            });
        }

        if (! Schema::hasTable('bahan_pasar')) {
            Schema::create('bahan_pasar', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('bahan_id')->nullable();
                $table->unsignedInteger('pasar_id')->nullable();
                $table->integer('warning')->nullable();

                $table->foreign('bahan_id')->references('id')->on('bahan')->cascadeOnDelete()->cascadeOnUpdate();
                $table->foreign('pasar_id')->references('id')->on('pasar')->cascadeOnDelete()->cascadeOnUpdate();
            });
        }

        if (! Schema::hasTable('berita')) {
            Schema::create('berita', function (Blueprint $table) {
                $table->increments('id');
                $table->string('judul')->nullable();
                $table->text('isi')->nullable();
                $table->unsignedInteger('kategori_id')->nullable();
                $table->string('file')->nullable();
                $table->timestamps();

                $table->foreign('kategori_id')->references('id')->on('kategori')->cascadeOnDelete()->cascadeOnUpdate();
            });
        }

        if (! Schema::hasTable('bulan_tahun')) {
            Schema::create('bulan_tahun', function (Blueprint $table) {
                $table->increments('id');
                $table->string('bulan')->nullable();
                $table->string('tahun')->nullable();
                $table->integer('pasar_id')->nullable();
            });
        }

        if (! Schema::hasTable('harga')) {
            Schema::create('harga', function (Blueprint $table) {
                $table->increments('id');
                $table->date('tanggal')->nullable();
                $table->unsignedInteger('harga')->nullable();
                $table->timestamps();
                $table->unsignedInteger('bahan_id')->nullable();
                $table->unsignedInteger('pasar_id')->nullable();

                $table->foreign('bahan_id')->references('id')->on('bahan')->cascadeOnDelete()->cascadeOnUpdate();
                $table->foreign('pasar_id')->references('id')->on('pasar')->cascadeOnDelete()->cascadeOnUpdate();
            });
        }

        if (! Schema::hasTable('harga_acuan')) {
            Schema::create('harga_acuan', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('bulan_tahun_id')->nullable();
                $table->integer('bahan_id')->nullable();
                $table->integer('harga')->nullable();
                $table->integer('pasar_id')->nullable();
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
