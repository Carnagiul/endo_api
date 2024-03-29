<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Players\API as PlayerAPI;
use App\Http\Controllers\Translations\API as TranslationsAPI;
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::group([
    'as' => 'api.',
], function() {
    Route::group([
        'prefix' => 'players',
        'as' => 'players.',
    ], function () {
        Route::get('list', [PlayerAPI::class, 'list'])->name('list');
        Route::get('find', [PlayerAPI::class, 'find'])->name('find');
        Route::get('info', [PlayerAPI::class, 'info'])->name('info');
        Route::get('connected', [PlayerAPI::class, 'connected'])->name('connected');
        Route::get('isMute', [PlayerAPI::class, 'isMute'])->name('isMute');
        Route::get("mute", [PlayerAPI::class, 'mute'])->name('mute');
        Route::get("unMute", [PlayerAPI::class, 'unMute'])->name('unMute');
        Route::get('isBan', [PlayerAPI::class, 'isBan'])->name('isBan');
        Route::get("ban", [PlayerAPI::class, 'ban'])->name('ban');
        Route::get("unBan", [PlayerAPI::class, 'unBan'])->name('unBan');
        Route::get('isFreeze', [PlayerAPI::class, 'isFreeze'])->name('isFreeze');
        Route::get("freeze", [PlayerAPI::class, 'freeze'])->name('freeze');
        Route::get("unFreeze", [PlayerAPI::class, 'unFreeze'])->name('unFreeze');
        Route::post('login', [PlayerAPI::class, 'login'])->name('login');
        Route::post('logout', [PlayerAPI::class, 'logout'])->name('logout');
        Route::get('register', [PlayerAPI::class, 'register'])->name('register');

        Route::group([
            'prefix' => "{player}",
            'as' => 'actions.'
        ], function() {
            Route::get("info", [PlayerAPI::class, 'playerInfo'])->name('info');
            Route::get("isBan", [PlayerAPI::class, 'playerIsBan'])->name('isBan');
            Route::get("isMute", [PlayerAPI::class, 'playerIsMute'])->name('isMute');
            Route::get("isFreeze", [PlayerAPI::class, 'playerIsFreeze'])->name('isFreeze');
            Route::get("isConnected", [PlayerAPI::class, 'playerIsConnected'])->name('isConnected');
            Route::get("stats", [PlayerAPI::class, 'playerStats'])->name('stats');
            Route::get("configs", [PlayerAPI::class, 'playerConfigs'])->name('configs');
            Route::group([
                'prefix' => 'configs',
                'as' => 'configs.'
            ], function() {
                Route::post("setLanguage", [PlayerAPI::class, 'playerSetLanguage'])->name('setLanguage');
                Route::post("setCoins", [PlayerAPI::class, 'playerSetCoins'])->name('setCoins');
                Route::post("addCoins", [PlayerAPI::class, 'playerAddCoins'])->name('addCoins');
                Route::post("removeCoins", [PlayerAPI::class, 'playerRemoveCoins'])->name('removeCoins');
                Route::post("setMoney", [PlayerAPI::class, 'playerSetMoney'])->name('setMoney');
                Route::post("addMoney", [PlayerAPI::class, 'playerAddMoney'])->name('addMoney');
                Route::post("removeMoney", [PlayerAPI::class, 'playerRemoveMoney'])->name('removeMoney');
                Route::post("setHidePlayer", [PlayerAPI::class, 'playerSetHidePlayer'])->name('setHidePlayer');
                Route::post("setConnectionNotification", [PlayerAPI::class, 'playerSetConnectionNotification'])->name('setConnectionNotification');
                Route::post("setFriendInvitation", [PlayerAPI::class, 'playerSetFriendInvitation'])->name('setFriendInvitation'); 
                Route::post("setFriendAcception", [PlayerAPI::class, 'playerSetFriendAcception'])->name('setFriendAcception');
                Route::post("setPingNotification", [PlayerAPI::class, 'playerSetPingNotification'])->name('setPingNotification');
            });
        });
    });
    Route::group([
        "prefix" => "translations",
        "as" => "translations." 
    ], function() {
        Route::get("list", [TranslationsAPI::class, 'list'])->name('list');
    });
});
