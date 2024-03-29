<?php

namespace App\Http\Controllers\Translations;

use App\Http\Controllers\Controller;
use App\Models\Translation;

class API extends Controller {

    public function list() {
        $translations = [];
        foreach(Translation::all() as $translation) {
            if (array_key_exists($translation->key, $translations) == false) {
                $translations[$translation->key] = [];
            }
            $translations[$translation->key][$translation->lang] = "[\"\",";
            foreach ($translation->components()->orderBy('order')->get() as $component) {
                if ($component->prev_id != null) {
                    $toFind = Translation::where('id', $component->prev_id)->first();
                    foreach ($toFind->components()->orderBy('order')->get() as $toFindComponent) {
                        $translations[$translation->key][$translation->lang] .= "{";

                        if ($toFindComponent->text != null) {
                            $translations[$translation->key][$translation->lang] .= "\"text\":\"" . $toFindComponent->text . "\",";
                        }
                        if ($toFindComponent->color != null) {
                            $translations[$translation->key][$translation->lang] .= "\"color\":\"" . $toFindComponent->color . "\",";
                        }
                        if ($toFindComponent->clickEventType != null) {
                            if ($toFindComponent->clickEventValue != null) {
                                $translations[$translation->key][$translation->lang] .= "\"clickEvent\":{\"action\":\"" . $toFindComponent->clickEventType . "\",\"value\":\"" . $toFindComponent->clickEventValue . "\"},";
                            }
                        }
                        if ($toFindComponent->hoverEventType != null) {
                            if ($toFindComponent->hoverEventContents != null) {
                                $translations[$translation->key][$translation->lang] .= "\"hoverEvent\":{\"action\":\"" . $toFindComponent->hoverEventType . "\",\"contents\":\"" . $toFindComponent->hoverEventContents . "\"},";
                            }
                        }
                        if ($toFindComponent->bold) {
                            $translations[$translation->key][$translation->lang] .= "\"bold\":true,";
                        }
                        if ($toFindComponent->italic) {
                            $translations[$translation->key][$translation->lang] .= "\"italic\":true,";
                        }
                        if ($toFindComponent->strikethrough) {
                            $translations[$translation->key][$translation->lang] .= "\"strikethrough\":true,";
                        }
                        if ($toFindComponent->underlined) {
                            $translations[$translation->key][$translation->lang] .= "\"underlined\":true,";
                        }
                        if ($toFindComponent->obfuscated) {
                            $translations[$translation->key][$translation->lang] .= "\"obfuscated\":true,";
                        }
                        $charAt = substr($translations[$translation->key][$translation->lang], -1);
                        if ($charAt == ",") {
                            $translations[$translation->key][$translation->lang] = substr($translations[$translation->key][$translation->lang], 0, -1);
                        }
                        $translations[$translation->key][$translation->lang] .= "},";
                    }
                } else {
                    $translations[$translation->key][$translation->lang] .= "{";

                        if ($component->text != null) {
                            $translations[$translation->key][$translation->lang] .= "\"text\":\"" . $component->text . "\",";
                        }
                        if ($component->color != null) {
                            $translations[$translation->key][$translation->lang] .= "\"color\":\"" . $component->color . "\",";
                        }
                        if ($component->clickEventType != null) {
                            if ($component->clickEventValue != null) {
                                $translations[$translation->key][$translation->lang] .= "\"clickEvent\":{\"action\":\"" . $component->clickEventType . "\",\"value\":\"" . $component->clickEventValue . "\"},";
                            }
                        }
                        if ($component->hoverEventType != null) {
                            if ($component->hoverEventContents != null) {
                                $translations[$translation->key][$translation->lang] .= "\"hoverEvent\":{\"action\":\"" . $component->hoverEventType . "\",\"contents\":\"" . $component->hoverEventContents . "\"},";
                            }
                        }
                        if ($component->bold) {
                            $translations[$translation->key][$translation->lang] .= "\"bold\":true,";
                        }
                        if ($component->italic) {
                            $translations[$translation->key][$translation->lang] .= "\"italic\":true,";
                        }
                        if ($component->strikethrough) {
                            $translations[$translation->key][$translation->lang] .= "\"strikethrough\":true,";
                        }
                        if ($component->underlined) {
                            $translations[$translation->key][$translation->lang] .= "\"underlined\":true,";
                        }
                        if ($component->obfuscated) {
                            $translations[$translation->key][$translation->lang] .= "\"obfuscated\":true,";
                        }
                        $charAt = substr($translations[$translation->key][$translation->lang], -1);
                        if ($charAt == ",") {
                            $translations[$translation->key][$translation->lang] = substr($translations[$translation->key][$translation->lang], 0, -1);
                        }
                        $translations[$translation->key][$translation->lang] .= "},";
                }

            }
            $charAt = substr($translations[$translation->key][$translation->lang], -1);
            if ($charAt == ",") {
                $translations[$translation->key][$translation->lang] = substr($translations[$translation->key][$translation->lang], 0, -1);
            }
            $translations[$translation->key][$translation->lang] .= "]";
        }
        return [
            'translations' => $translations
        ];
    }

}