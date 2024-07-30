<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Psr\Log\LogLevel;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('change_logs', function (Blueprint $table) {
            $table->id();

            $table->string('level')
                ->default(LogLevel::INFO)
                ->index();

            $table->unsignedBigInteger('user_id')
                ->nullable()
                ->index();

            $table->string('entity_type')
                ->nullable()
                ->index();

            $table->unsignedBigInteger('entity_id')
                ->nullable()
                ->index();

            $table->longText('message')
                ->nullable()
                ->fulltext();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('change_logs');
    }
};
