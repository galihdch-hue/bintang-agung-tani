<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
  public function register(Request $request)
  {
    $request->validate([
      'name' => 'required|string|max:255',
      'email' => 'required|string|email|max:255|unique:users',
      'password' => 'required|string|min:8|confirmed',
      'phone' => 'nullable|string|max:20',
    ]);

    $user = User::create([
      'name' => $request->name,
      'email' => $request->email,
      'password' => Hash::make($request->password),
      'phone' => $request->phone,
    ]);

    $token = $user->createToken('auth_token')->plainTextToken;

    return response()->json([
      'access_token' => $token,
      'token_type' => 'Bearer',
      'user' => $this->formatUser($user),
    ], 201);
  }

  public function login(Request $request)
  {
    $request->validate([
      'email' => 'required|string|email',
      'password' => 'required|string',
    ]);

    $user = User::where('email', $request->email)->first();

    if (! $user || ! Hash::check($request->password, $user->password)) {
      throw ValidationException::withMessages([
        'email' => ['Kredensial yang diberikan salah.'],
      ]);
    }

    $token = $user->createToken('auth_token')->plainTextToken;

    return response()->json([
      'access_token' => $token,
      'token_type' => 'Bearer',
      'user' => $this->formatUser($user),
    ]);
  }

  public function logout(Request $request)
  {
    $token = $request->user()?->currentAccessToken();

    if ($token) {
      $token->delete();
    }

    return response()->json([
      'message' => 'Berhasil keluar akun.'
    ]);
  }

  public function me(Request $request)
  {
    return response()->json($this->formatUser($request->user()));
  }

  private function formatUser(User $user): array
  {
    $defaultAddress = $user->default_address;

    return [
      'id' => $user->id,
      'name' => $user->name,
      'email' => $user->email,
      'phone' => $user->phone,
      'address' => $user->address,
      'profile_photo_url' => $user->profile_photo_url,
      'monthly_spending' => $user->getFormattedMonthlySpending(),
      'default_address' => $defaultAddress ? $this->formatAddress($defaultAddress) : null,
    ];
  }

  private function formatAddress(?Address $address): ?array
  {
    if (! $address) {
      return null;
    }

    return [
      'id' => $address->id,
      'label' => $address->label,
      'title' => $address->label,
      'recipient_name' => $address->recipient_name,
      'receiver_name' => $address->recipient_name,
      'phone' => $address->phone,
      'receiver_phone' => $address->phone,
      'full_address' => $address->full_address,
      'detail' => $address->complete_address,
      'province' => $address->province,
      'city' => $address->city,
      'district' => $address->district,
      'postal_code' => $address->postal_code,
      'is_default' => $address->is_default,
      'notes' => $address->notes,
      'complete_address' => $address->complete_address,
    ];
  }
}
