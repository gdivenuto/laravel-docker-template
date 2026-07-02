<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFkNormaIdToDescriptorNorma extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Se agrega la foreign key
        DB::statement('ALTER TABLE `descriptor_norma` ADD CONSTRAINT `descriptor_norma_norma_id_foreign` FOREIGN KEY (`norma_id`) REFERENCES `normas` (`id`);');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Se elimina la foreign key
        DB::statement('ALTER TABLE `descriptor_norma` DROP FOREIGN KEY `descriptor_norma_norma_id_foreign`;');
    }
}
