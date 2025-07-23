<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Migrate existing ward-specialty relationships from single foreign key to many-to-many
        $wards = DB::table('wards')
            ->whereNotNull('specialty_id')
            ->get();
            
        foreach ($wards as $ward) {
            // Check if this relationship doesn't already exist in the pivot table
            $exists = DB::table('ward_specialty')
                ->where('ward_id', $ward->id)
                ->where('specialty_id', $ward->specialty_id)
                ->exists();
                
            if (!$exists) {
                DB::table('ward_specialty')->insert([
                    'ward_id' => $ward->id,
                    'specialty_id' => $ward->specialty_id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove all ward-specialty relationships from pivot table
        // (the single specialty_id foreign key will remain for backward compatibility)
        DB::table('ward_specialty')->truncate();
    }
}; 