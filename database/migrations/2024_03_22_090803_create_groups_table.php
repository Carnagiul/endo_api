<?php

use App\Models\Group;
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
        Schema::create('groups', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('color');
            $table->integer('priority')->default(0);
            $table->boolean('default')->default(false);
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->timestamps();
        });

        $playerGroup = new Group();
        $playerGroup->name = 'player';
        $playerGroup->color = 'yellow';
        $playerGroup->priority = 0;
        $playerGroup->default = true;
        $playerGroup->save();

        $vipGroup = new Group();
        $vipGroup->name = 'vip';
        $vipGroup->color = 'green';
        $vipGroup->priority = 1;
        $vipGroup->default = false;
        $vipGroup->parent_id = $playerGroup->id;
        $vipGroup->save();

        $helperGroup = new Group();
        $helperGroup->name = 'helper';
        $helperGroup->color = 'light_blue';   
        $helperGroup->priority = 2;
        $helperGroup->default = false;
        $helperGroup->parent_id = $vipGroup->id;
        $helperGroup->save();

        $moderatorGroup = new Group();
        $moderatorGroup->name = 'moderator';
        $moderatorGroup->color = 'blue';
        $moderatorGroup->priority = 3;
        $moderatorGroup->default = false;
        $moderatorGroup->parent_id = $helperGroup->id;
        $moderatorGroup->save();

        $builderGroup = new Group();
        $builderGroup->name = 'builder';
        $builderGroup->color = 'light_purple';
        $builderGroup->priority = 4;
        $builderGroup->default = false;
        $builderGroup->parent_id = $moderatorGroup->id;
        $builderGroup->save();

        $devGroup = new Group();
        $devGroup->name = 'dev';
        $devGroup->color = 'light_purple';
        $devGroup->priority = 5;
        $devGroup->default = false;
        $devGroup->parent_id = $devGroup->id;
        $devGroup->save();

        $adminGroup = new Group();
        $adminGroup->name = 'admin';
        $adminGroup->color = 'dark_red';
        $adminGroup->priority = 4;
        $adminGroup->default = false;
        $adminGroup->parent_id = $devGroup->id;
        $adminGroup->save();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('groups');
    }
};
