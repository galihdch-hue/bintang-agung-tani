<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAddressRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
  public function profile(Request $request): JsonResponse
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
      'addresses_count' => $user->addresses()->count(),
      'default_address' => $this->formatAddress($user->default_address),
    ]);
  }

  public function updateProfile(UpdateProfileRequest $request): JsonResponse
  {
    $user = $request->user();
    $validated = $request->validated();

    if ($request->hasFile('profile_photo')) {
      if (! empty($user->profile_photo_path)) {
        Storage::disk('public')->delete($user->profile_photo_path);
      }

      $validated['profile_photo_path'] = $request->file('profile_photo')->store('profile-photos', 'public');
    }

    unset($validated['profile_photo']);

    $user->update($validated);

    return $this->profile($request);
  }

  public function destroyProfilePhoto(Request $request): JsonResponse
  {
    $user = $request->user();

    if (! empty($user->profile_photo_path)) {
      Storage::disk('public')->delete($user->profile_photo_path);
    }

    $user->update([
      'profile_photo_path' => null,
    ]);

    return response()->json([
      'message' => 'Foto profil berhasil dihapus.',
      'user' => $this->profile($request)->getData(true),
    ]);
  }

  public function addresses(Request $request): JsonResponse
  {
    $addresses = $request->user()
      ->addresses()
      ->orderByDesc('is_default')
      ->orderByDesc('created_at')
      ->get()
      ->map(fn(Address $address) => $this->formatAddress($address))
      ->values();

    return response()->json($addresses);
  }

  public function storeAddress(StoreAddressRequest $request): JsonResponse
  {
    $user = $request->user();
    $validated = $request->validated();
    $validated['user_id'] = $user->id;

    if (! empty($validated['is_default'])) {
      $user->addresses()->where('is_default', true)->update(['is_default' => false]);
    }

    if ($user->addresses()->count() === 0) {
      $validated['is_default'] = true;
    }

    $address = Address::create($validated);

    return response()->json([
      'message' => 'Alamat berhasil ditambahkan.',
      'address' => $this->formatAddress($address),
    ], 201);
  }

  public function updateAddress(StoreAddressRequest $request, Address $address): JsonResponse
  {
    $this->ensureAddressOwnership($request, $address);

    $user = $request->user();
    $validated = $request->validated();

    if (! empty($validated['is_default']) && ! $address->is_default) {
      $user->addresses()->where('is_default', true)->update(['is_default' => false]);
    }

    $address->update($validated);

    return response()->json([
      'message' => 'Alamat berhasil diperbarui.',
      'address' => $this->formatAddress($address->fresh()),
    ]);
  }

  public function destroyAddress(Request $request, Address $address): JsonResponse
  {
    $this->ensureAddressOwnership($request, $address);

    $wasDefault = $address->is_default;
    $address->delete();

    if ($wasDefault) {
      $newDefault = $request->user()->addresses()->orderByDesc('created_at')->first();

      if ($newDefault) {
        $newDefault->update(['is_default' => true]);
      }
    }

    return response()->json([
      'message' => 'Alamat berhasil dihapus.',
    ]);
  }

  public function setDefaultAddress(Request $request, Address $address): JsonResponse
  {
    $this->ensureAddressOwnership($request, $address);

    $user = $request->user();
    $user->addresses()->where('is_default', true)->update(['is_default' => false]);
    $address->update(['is_default' => true]);

    return response()->json([
      'message' => 'Alamat utama berhasil diubah.',
      'address' => $this->formatAddress($address->fresh()),
    ]);
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

  private function ensureAddressOwnership(Request $request, Address $address): void
  {
    abort_unless($address->user_id === $request->user()->id, 403, 'Anda tidak memiliki akses untuk alamat ini.');
  }

  public function changePassword(Request $request): JsonResponse
  {
    $request->validate([
      'current_password' => 'required|string',
      'password' => 'required|string|min:8|confirmed',
    ]);

    $user = $request->user();

    if (!Hash::check($request->input('current_password'), $user->password)) {
      return response()->json([
        'message' => 'Password saat ini tidak sesuai.',
      ], 422);
    }

    $user->update([
      'password' => Hash::make($request->input('password')),
    ]);

    return response()->json([
      'message' => 'Password berhasil diubah.',
    ]);
  }
}
