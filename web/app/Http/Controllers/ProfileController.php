<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function show()
    {
        $user = Auth::user();

        $createdSchedules = Schedule::query()
            ->where('owner_id', $user->id)
            ->orderBy('name')
            ->get();

        return view('profile.show', compact(
            'user',
            'createdSchedules'
        ));
    }
}