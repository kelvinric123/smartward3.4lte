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
        Schema::table('consultants', function (Blueprint $table) {
            $table->string('code', 20)->nullable()->unique()->after('id')->comment('Unique consultant code for HL7 integration');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('consultants', function (Blueprint $table) {
            $table->dropColumn('code');
        });
    }
};
