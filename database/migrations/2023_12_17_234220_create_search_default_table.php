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
        Schema::create('search_default', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->unsigned()->nullable();
            $table->foreign('user_id')
                        ->references('id')->on('users')
                        ->onDelete('cascade'); 
            $table->integer('search_id')->unsigned()->nullable();
            $table->foreign('search_id')
                        ->references('id')->on('search')
                        ->onDelete('SET NULL');  
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('search_default');
    }
};
