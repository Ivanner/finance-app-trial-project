<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImportProcessesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'import_processes',
            function (Blueprint $table) {
                $table->bigInteger('id')->autoIncrement()->unsigned();
                $table->string('fileName')->unique();
                $table->foreignId('user_id');
                $table->boolean('imported');
                $table->integer('rowsImported');
                $table->integer('totalRows');
                $table->timestamps();
            }
        );
        Schema::table(
            'transactions',
            function (Blueprint $table) {
                $table->foreignId('import_process_id')->nullable(true)->constrained();
            }
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('import_processes');
    }
}
