<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVersionsApprovalFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('versions', function(Blueprint $table) {
            $table->timestamp('approved_at')->nullable()->default(null);
            $table->string('approved_by')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('versions', function(Blueprint $table) {
            $table->dropColumn('approved_at');
            $table->dropColumn('approved_by');
        });
    }
}
