<?php

use App\Models\Translation;
use App\Models\TranslationComponent;
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
        Schema::create('translations', function (Blueprint $table) {
            $table->id();
            $table->string('key');
            $table->string('lang')->default('en');
            $table->unique(['key', 'lang']);
            $table->timestamps();
        });

        Schema::create('translation_components', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('translation_id');
            $table->unsignedInteger('order');
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->unsignedBigInteger('prev_id')->nullable();
            $table->string('text')->nullable();
            $table->string('color')->nullable();
            $table->string('clickEventType')->nullable();
            $table->string('clickEventValue')->nullable();
            $table->string('hoverEventType')->nullable();
            $table->string('hoverEventContents')->nullable();
            $table->boolean('bold')->default(false);
            $table->boolean('italic')->default(false);
            $table->boolean('strikethrough')->default(false);
            $table->boolean('underlined')->default(false);
            $table->boolean('obfuscated')->default(false);
            $table->boolean('team_color')->default(false);
            $table->timestamps();
            $table->unique(['translation_id', 'order']);

            $table->foreign('translation_id')->references('id')->on('translations')->onDelete('cascade');
            $table->foreign('prev_id')->references('id')->on('translations')->onDelete('cascade');
        });

        $joinTag = Translation::create([
            'key' => 'player_join_tag',
            'lang' => 'en'
        ]);

        $joinTag->save();

        $playerTag = Translation::create([
            'key' => 'player_tag',
            'lang' => 'en'
        ]);

        $playerTagAdmin = Translation::create([
            'key' => 'player_tag_admin',
            'lang' => 'en'
        ]);

        $playerTag->save();
        $playerTagAdmin->save();

        $playerTag->components()->create([
            'order' => 0,
            'translation_id' => $playerTag->id,
            'text' => ' playerName',
            'color' => 'yellow',
            'team_color' => true,
        ]);

        $playerTagAdmin->components()->create([
            'order' => 0,
            'translation_id' => $playerTagAdmin->id,
            'text' => ' playerName',
            'color' => 'yellow',
            'team_color' => true,
            'clickEventType' => 'copy_to_clipboard',
            'clickEventValue' => 'playerUUID',
            'hoverEventType' => 'show_text',
            'hoverEventContents' => 'Click to copy the UUID of playerName\\n UUID : playerUUID',
        ]);

        $joinTag->components()->create([
            'order' => 0,
            'text' => '[',
            'color' => 'white',
        ]);

        $joinTag->components()->create([
            'order' => 1,
            'text' => '+',
            'color' => 'dark_green',
        ]);

        $joinTag->components()->create([
            'order' => 2,
            'text' => ']',
            'color' => 'white',
        ]);

        $joinEn = Translation::create([
            'key' => 'player_join',
            'lang' => 'en'
        ]);
        $joinEnAdmin = Translation::create([
            'key' => 'admin_player_join',
            'lang' => 'en'
        ]);

        $joinEn->save();
        $joinEnAdmin->save();

        $joinFr = Translation::create([
            'key' => 'player_join',
            'lang' => 'fr'
        ]);
        $joinFrAdmin = Translation::create([
            'key' => 'admin_player_join',
            'lang' => 'fr'
        ]);

        $joinFr->save();
        $joinFrAdmin->save();

        $joinEn->components()->create([
            'order' => 0,
            'translation_id' => $joinEn->id,
            'prev_id' => $joinTag->id,
        ]);
        $joinEn->components()->create([
            'order' => 1,
            'translation_id' => $joinEn->id,
            'prev_id' => $playerTag->id,
        ]);
        $joinEn->components()->create([
            'order' => 2,
            'translation_id' => $joinEn->id,
            'text' => ' joined the game.',
        ]);

        $joinEnAdmin->components()->create([
            'order' => 0,
            'translation_id' => $joinEnAdmin->id,
            'prev_id' => $joinTag->id,
        ]);
        $joinEnAdmin->components()->create([
            'order' => 1,
            'translation_id' => $joinEnAdmin->id,
            'prev_id' => $playerTagAdmin->id,
        ]);
        $joinEnAdmin->components()->create([
            'order' => 2,
            'translation_id' => $joinEnAdmin->id,
            'text' => ' joined the game.',
        ]);

        
        $joinFr->components()->create([
            'order' => 0,
            'translation_id' => $joinFr->id,
            'prev_id' => $joinTag->id,
        ]);
        $joinFr->components()->create([
            'order' => 1,
            'translation_id' => $joinFr->id,
            'prev_id' => $playerTag->id,
        ]);
        $joinFr->components()->create([
            'order' => 2,
            'translation_id' => $joinFr->id,
            'text' => ' est en ligne.',
        ]);

        $joinFrAdmin->components()->create([
            'order' => 0,
            'translation_id' => $joinFrAdmin->id,
            'prev_id' => $joinTag->id,
        ]);
        $joinFrAdmin->components()->create([
            'order' => 1,
            'translation_id' => $joinFrAdmin->id,
            'prev_id' => $playerTagAdmin->id,
        ]);
        $joinFrAdmin->components()->create([
            'order' => 2,
            'translation_id' => $joinFrAdmin->id,
            'text' => ' est en ligne.',
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('translation_components');
        Schema::dropIfExists('translations');
    }
};
