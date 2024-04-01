<?php

namespace App\Http\Controllers\Groups;

use App\Http\Controllers\Controller;
use App\Models\Group;

class API extends Controller {

   public function list() {
    return response()->json([
        'groups' => Group::all()
    ]);
   }
}