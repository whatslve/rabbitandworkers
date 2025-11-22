<?php

namespace App\Http\Controllers;

use App\Models\Notification;

class NotificationController extends Controller
{
    public function index() {
        $data = Notification::all();
        return response()->json($data, 200);
    }
}
