<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDescriptorDigesto extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('descriptor_digesto', function (Blueprint $table) {
            $table->foreignId('descriptor_id')->constrained('descriptores');
            $table->foreignId('digesto_id')->constrained('digestos');
            
            // forma en la que se contempla el descriptor para filtrar
            $table->enum('condicion', ['and', 'or'])->default('or'); 
            
            $table->timestamps();

            $table->primary(['descriptor_id', 'digesto_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('descriptor_digesto');
    }
}
