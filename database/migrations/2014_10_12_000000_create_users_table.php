<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mst_users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('fullname');
            $table->string('phone_number')->nullable();
            $table->string('email')->unique();
            $table->string('username')->unique();
            $table->string('password');
            $table->integer('is_active')->default(1);
            $table->uuid('role_id');
            $table->string('token')->nullable();
            $table->timestamp('created_date')->useCurrent();
            $table->uuid('created_by')->nullable();
            $table->timestamp('updated_date')->useCurrent()->useCurrentOnUpdate();
            $table->uuid('updated_by')->nullable();
            $table->timestamp('deleted_date')->nullable();
            $table->uuid('deleted_by')->nullable();
            
            $table->foreign('role_id')->references('id')->on('mst_roles')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mst_users');
    }
};
