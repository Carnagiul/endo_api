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

        $tagSystem = Translation::create([
            'key' => 'tag_system',
            'lang' => 'en'
        ]);
        $tagSystem->save();
        $tagSystem->components()->create([
            'order' => 0,
            'translation_id' => $tagSystem->id,
            'text' => '[',
            'color' => 'white',
        ]);
        $tagSystem->components()->create([
            'order' => 1,
            'translation_id' => $tagSystem->id,
            'text' => 'System',
            'color' => 'light_purple',
        ]);
        $tagSystem->components()->create([
            'order' => 2,
            'translation_id' => $tagSystem->id,
            'text' => ']',
            'color' => 'white',
        ]);

        $playerHideAllPlayersEn = Translation::create([
            'key' => 'player_hide_all_players',
            'lang' => 'en'
        ]);
        $playerHideAllPlayersEn->save();
        $playerHideAllPlayersFr = Translation::create([
            'key' => 'player_hide_all_players',
            'lang' => 'fr'
        ]);
        $playerHideAllPlayersFr->save();

        $playerHideAllPlayersEn->components()->create([
            'order' => 0,
            'translation_id' => $playerHideAllPlayersEn->id,
            'prev_id' => $tagSystem->id,
        ]);
        $playerHideAllPlayersEn->components()->create([
            'order' => 1,
            'translation_id' => $playerHideAllPlayersEn->id,
            'text' => ' You have hidden all players.',
            'color' => 'green',
        ]);

        $playerHideAllPlayersFr->components()->create([
            'order' => 0,
            'translation_id' => $playerHideAllPlayersFr->id,
            'prev_id' => $tagSystem->id,
        ]);
        $playerHideAllPlayersFr->components()->create([
            'order' => 1,
            'translation_id' => $playerHideAllPlayersFr->id,
            'text' => ' Vous avez caché tous les joueurs.',
            'color' => 'green',
        ]);
        $playerShowAllPlayersEn = Translation::create([
            'key' => 'player_show_all_players',
            'lang' => 'en'
        ]);
        $playerShowAllPlayersEn->save();
        $playerShowAllPlayersFr = Translation::create([
            'key' => 'player_show_all_players',
            'lang' => 'fr'
        ]);
        $playerShowAllPlayersFr->save();

        $playerShowAllPlayersEn->components()->create([
            'order' => 0,
            'translation_id' => $playerShowAllPlayersEn->id,
            'prev_id' => $tagSystem->id,
        ]);
        $playerShowAllPlayersEn->components()->create([
            'order' => 1,
            'translation_id' => $playerShowAllPlayersEn->id,
            'text' => ' You show all players.',
            'color' => 'green',
        ]);

        $playerShowAllPlayersFr->components()->create([
            'order' => 0,
            'translation_id' => $playerShowAllPlayersFr->id,
            'prev_id' => $tagSystem->id,
        ]);
        $playerShowAllPlayersFr->components()->create([
            'order' => 1,
            'translation_id' => $playerShowAllPlayersFr->id,
            'text' => ' Vous affichez tous les joueurs.',
            'color' => 'green',
        ]);

        $playerHidePlayerErrorEn = Translation::create([
            'key' => 'player_hide_all_player_error',
            'lang' => 'en'
        ]);
        $playerHidePlayerErrorEn->save();
        $playerHidePlayerErrorFr = Translation::create([
            'key' => 'player_hide_all_player_error',
            'lang' => 'fr'
        ]);
        $playerHidePlayerErrorFr->save();

        $playerHidePlayerErrorEn->components()->create([
            'order' => 0,
            'translation_id' => $playerHidePlayerErrorEn->id,
            'prev_id' => $tagSystem->id,
        ]);
        $playerHidePlayerErrorEn->components()->create([
            'order' => 1,
            'translation_id' => $playerHidePlayerErrorEn->id,
            'text' => ' You can\'t hide all players before',
            'color' => 'red',
        ]);
        $playerHidePlayerErrorEn->components()->create([
            'order' => 2,
            'translation_id' => $playerHidePlayerErrorEn->id,
            'text' => ' timestampParsed.',
            'color' => 'gold',
        ]);

        $playerHidePlayerErrorFr->components()->create([
            'order' => 0,
            'translation_id' => $playerHidePlayerErrorFr->id,
            'prev_id' => $tagSystem->id,
        ]);
        $playerHidePlayerErrorFr->components()->create([
            'order' => 1,
            'translation_id' => $playerHidePlayerErrorFr->id,
            'text' => ' Vous ne pouvez pas cacher tous les joueurs avant',
            'color' => 'red',
        ]);
        $playerHidePlayerErrorFr->components()->create([
            'order' => 2,
            'translation_id' => $playerHidePlayerErrorFr->id,
            'text' => ' timestampParsed.',
            'color' => 'gold',
        ]);

        $playerShowPlayerErrorEn = Translation::create([
            'key' => 'player_show_all_player_error',
            'lang' => 'en'
        ]);
        $playerShowPlayerErrorEn->save();
        $playerShowPlayerErrorFr = Translation::create([
            'key' => 'player_show_all_player_error',
            'lang' => 'fr'
        ]);
        $playerShowPlayerErrorFr->save();

        $playerShowPlayerErrorEn->components()->create([
            'order' => 0,
            'translation_id' => $playerShowPlayerErrorEn->id,
            'prev_id' => $tagSystem->id,
        ]);
        $playerShowPlayerErrorEn->components()->create([
            'order' => 1,
            'translation_id' => $playerShowPlayerErrorEn->id,
            'text' => ' You can\'t show all players before',
            'color' => 'red',
        ]);
        $playerShowPlayerErrorEn->components()->create([
            'order' => 2,
            'translation_id' => $playerShowPlayerErrorEn->id,
            'text' => ' timestampParsed.',
            'color' => 'gold',
        ]);

        $playerShowPlayerErrorFr->components()->create([
            'order' => 0,
            'translation_id' => $playerShowPlayerErrorFr->id,
            'prev_id' => $tagSystem->id,
        ]);
        $playerShowPlayerErrorFr->components()->create([
            'order' => 1,
            'translation_id' => $playerShowPlayerErrorFr->id,
            'text' => ' Vous ne pouvez pas afficher tous les joueurs avant',
            'color' => 'red',
        ]);
        $playerShowPlayerErrorFr->components()->create([
            'order' => 2,
            'translation_id' => $playerShowPlayerErrorFr->id,
            'text' => ' timestampParsed.',
            'color' => 'gold',
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
