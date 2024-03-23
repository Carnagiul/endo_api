<?php

use App\Models\Player;
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
        $carna = new Player([
            'name' => 'TonPeyre',
            'uuid' => '9c7f918a-cbaf-47c9-ab0f-b1a8b57b61ab',
            'group' => 'admin',
        ]);

        $drSallan = new Player([
            'name' => 'DrSallan',
            'uuid' => '3433b342-926d-4a59-92ad-053fde6107b4',
        ]);

        $carna->save();
        $drSallan->save();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('player_configs');
    }
};
