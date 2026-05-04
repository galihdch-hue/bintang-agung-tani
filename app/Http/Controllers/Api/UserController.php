<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function profile(Request $request)
    {
        $user = $request->user();
        
        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'address' => $user->address,
            'profile_photo_url' => $user->profile_photo_url,
            'monthly_spending' => $user->getFormattedMonthlySpending(),
        ]);
    }

    public function addresses(Request $request)
    {
        $user = $request->user();
        $addresses = $user->addresses()->get();
        
        return response()->json($addresses);
    }
}
