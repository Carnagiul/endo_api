<?php

use App\Models\Group;
use App\Models\GroupConfig;
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
        $this->down();
        
        Schema::create('group_configs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('group_id');
            $table->string('key');
            $table->text('value');
            $table->timestamps();
            $table->foreign('group_id')->references('id')->on('groups')->onDelete('cascade');
            $table->unique(['group_id', 'key']);
        });

        Group::all()->each(function ($group) {
            $config = new GroupConfig();
            $config->group_id = $group->id;
            $config->key = 'list_name';
            $config->value = strtolower($group->name);
            $config->save();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('group_configs');
    }
};
