<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFkDescriptorIdToDescriptorNorma extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Se agrega la foreign key
        DB::statement('ALTER TABLE `descriptor_norma` ADD CONSTRAINT `descriptor_norma_descriptor_id_foreign` FOREIGN KEY (`descriptor_id`) REFERENCES `descriptores` (`id`);');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Se elimina la foreign key
        DB::statement('ALTER TABLE `descriptor_norma` DROP FOREIGN KEY `descriptor_norma_descriptor_id_foreign`;');
    }
}
