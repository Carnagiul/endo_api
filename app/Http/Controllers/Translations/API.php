<?php

namespace App\Http\Controllers\Translations;

use App\Http\Controllers\Controller;
use App\Models\Translation;
use App\Models\TranslationComponent;

class API extends Controller {

    public function componentToStr(TranslationComponent $component, Translation $translation, &$translations) {
        if ($component->prev_id != null) {
            $toFind = Translation::where('id', $component->prev_id)->first();
            foreach ($toFind->components()->orderBy('order')->get() as $toFindComponent) {
                $this->componentToStr($toFindComponent, $translation, $translations);
            }
        } else {
            $translations[$translation->key][$translation->lang] .= '{';

            if ($component->text != null) {
                $translations[$translation->key][$translation->lang] .= '"text":"' . $component->text . '",';
            }
            if ($component->color != null) {
                $translations[$translation->key][$translation->lang] .= '"color":"' . $component->color . '",';
            }
            if ($component->clickEventType != null) {
                if ($component->clickEventValue != null) {
                    $translations[$translation->key][$translation->lang] .= '"clickEvent":{"action":"' . $component->clickEventType . '","value":"' . $component->clickEventValue . '"},';
                }
            }
            if ($component->hoverEventType != null) {
                if ($component->hoverEventContents != null) {
                    $translations[$translation->key][$translation->lang] .= '"hoverEvent":{"action":"' . $component->hoverEventType . '","contents":"' . $component->hoverEventContents . '"},';
                }
            }
            if ($component->bold) {
                $translations[$translation->key][$translation->lang] .= '"bold":true,';
            }
            if ($component->italic) {
                $translations[$translation->key][$translation->lang] .= '"italic":true,';
            }
            if ($component->strikethrough) {
                $translations[$translation->key][$translation->lang] .= '"strikethrough":true,';
            }
            if ($component->underlined) {
                $translations[$translation->key][$translation->lang] .= '"underlined":true,';
            }
            if ($component->obfuscated) {
                $translations[$translation->key][$translation->lang] .= '"obfuscated":true,';
            }
            $charAt = substr($translations[$translation->key][$translation->lang], -1);
            if ($charAt == ',') {
                $translations[$translation->key][$translation->lang] = substr($translations[$translation->key][$translation->lang], 0, -1);
            }
            $translations[$translation->key][$translation->lang] .= '},';
        }
    }

    public function list() {
        $translations = ['keys' => []];
        foreach(Translation::all() as $translation) {
            if (array_key_exists($translation->key, $translations) == false) {
                $translations['keys'][] = $translation->key;
                $translations[$translation->key] = ['lang' => [], 'key' => $translation->key];
            }
            $translations[$translation->key]['lang'][] = $translation->lang;
            $translations[$translation->key][$translation->lang] = '[",';
            foreach ($translation->components()->orderBy('order')->get() as $component) {
                if ($component->prev_id != null) {
                    $toFind = Translation::where('id', $component->prev_id)->first();
                    foreach ($toFind->components()->orderBy('order')->get() as $toFindComponent) {
                        $this->componentToStr($toFindComponent, $translation, $translations);
                    }
                } else {
                    $this->componentToStr($component, $translation, $translations);
                }

            }
            $charAt = substr($translations[$translation->key][$translation->lang], -1);
            if ($charAt == ',') {
                $translations[$translation->key][$translation->lang] = substr($translations[$translation->key][$translation->lang], 0, -1);
            }
            $translations[$translation->key][$translation->lang] .= ']';
        }
        return [
            'translations' => $translations
        ];
    }

}