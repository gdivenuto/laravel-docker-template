<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTriggers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("
            CREATE TRIGGER trg_normas_beforeinsert 
            BEFORE INSERT ON `normas` 
            FOR EACH ROW
                SET NEW.acto_nro = IF(NEW.acto_nro IS NULL, CONCAT(IFNULL(NEW.acto, ''), IFNULL(NEW.nro, '')), NEW.acto_nro);
        ");

        DB::unprepared("
            CREATE TRIGGER trg_normas_beforeupdate 
            BEFORE UPDATE ON `normas` 
            FOR EACH ROW
                SET NEW.acto_nro = IF(NEW.acto_nro <=> OLD.acto_nro, CONCAT(IFNULL(NEW.acto, ''), IFNULL(NEW.nro, '')), NEW.acto_nro);
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared("DROP TRIGGER `trg_normas_beforeupdate`");
        DB::unprepared("DROP TRIGGER `trg_normas_beforeinsert`");
    }
}
