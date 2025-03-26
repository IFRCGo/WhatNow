<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameRegionsToSubnational extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Eliminar índices existentes
        Schema::table('regions', function (Blueprint $table) {
            $table->dropIndex('regions_organisation_id_index');
            $table->dropIndex('regions_slug_index');
        });

        // Renombrar tabla
        Schema::rename('regions', 'subnational');

        // Crear nuevos índices en la tabla renombrada
        Schema::table('subnational', function (Blueprint $table) {
            $table->index('organisation_id', 'subnational_organisation_id_index');
            $table->index('slug', 'subnational_slug_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Eliminar índices de la tabla renombrada
        Schema::table('subnational', function (Blueprint $table) {
            $table->dropIndex('subnational_organisation_id_index');
            $table->dropIndex('subnational_slug_index');
        });

        // Revertir el renombramiento de la tabla
        Schema::rename('subnational', 'regions');

        // Recrear los índices originales
        Schema::table('regions', function (Blueprint $table) {
            $table->index('organisation_id', 'regions_organisation_id_index');
            $table->index('slug', 'regions_slug_index');
        });
    }
}
