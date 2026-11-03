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
        if (! Schema::hasTable('roles')) {
            Schema::create('roles', function (Blueprint $table) {
                $table->id();
                $table->string('name', 50);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('role_users')) {
            Schema::create('role_users', function (Blueprint $table) {
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('role_id');
                $table->unique(['user_id', 'role_id']);
                $table->foreign('role_id')->references('id')->on('roles')->cascadeOnDelete()->cascadeOnUpdate();
                $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete()->cascadeOnUpdate();
            });
        }

        if (! Schema::hasTable('satuan')) {
            Schema::create('satuan', function (Blueprint $table) {
                $table->increments('id');
                $table->string('nama')->nullable();
            });
        }

        if (! Schema::hasTable('kelompok')) {
            Schema::create('kelompok', function (Blueprint $table) {
                $table->increments('id');
                $table->string('nama')->nullable();
            });
        }

        if (! Schema::hasTable('kategori')) {
            Schema::create('kategori', function (Blueprint $table) {
                $table->increments('id');
                $table->string('nama')->nullable();
            });
        }

        if (! Schema::hasTable('pasar')) {
            Schema::create('pasar', function (Blueprint $table) {
                $table->increments('id');
                $table->string('nama')->nullable();
                $table->string('lat')->nullable();
                $table->string('long')->nullable();
                $table->integer('radius')->nullable();
                $table->char('tampil_stok', 1)->default('Y')->nullable();
            });
        }

        if (! Schema::hasTable('pasar_users')) {
            Schema::create('pasar_users', function (Blueprint $table) {
                $table->integer('pasar_id')->nullable();
                $table->integer('user_id')->nullable();
            });
        }

        if (! Schema::hasTable('nomor_aduan')) {
            Schema::create('nomor_aduan', function (Blueprint $table) {
                $table->increments('id');
                $table->string('nomor')->nullable();
            });
        }

        if (! Schema::hasTable('notifikasi')) {
            Schema::create('notifikasi', function (Blueprint $table) {
                $table->increments('id');
                $table->string('nomor')->nullable();
            });
        }

        if (! Schema::hasTable('traffic')) {
            Schema::create('traffic', function (Blueprint $table) {
                $table->increments('id');
                $table->date('tanggal')->nullable();
                $table->string('ip')->nullable();
                $table->text('browser')->nullable();
                $table->string('negara')->nullable();
                $table->string('os')->nullable();
                $table->string('device')->nullable();
            });
        }

        if (! Schema::hasTable('slider')) {
            Schema::create('slider', function (Blueprint $table) {
                $table->increments('id');
                $table->string('file')->nullable();
                $table->string('judul')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('cronjob')) {
            Schema::create('cronjob', function (Blueprint $table) {
                $table->increments('id');
                $table->string('cronjob')->nullable();
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
