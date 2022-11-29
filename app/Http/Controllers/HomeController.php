<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index()
    {
        $events = DB::select(DB::raw('select id,
        name,
        status,
        note,
        color,
        start as startDate,
        end as endDate from calendar_events;'));
        return view('welcome')->with('events', $events);
    }
}
