<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
  /** @use HasFactory<\Database\Factories\UserFactory> */
  use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

  /**
   * The attributes that are mass assignable.
   *
   * @var list<string>
   */
  protected $fillable = [
    'name',
    'email',
    'password',
    'phone',
    'address',
    'profile_photo_path',
  ];

  /**
   * The attributes that should be hidden for serialization.
   *
   * @var list<string>
   */
  protected $hidden = [
    'password',
    'remember_token',
  ];

  /**
   * The model's default values for attributes.
   *
   * @var array
   */
  protected $attributes = [
    'is_admin' => false,
  ];

  /**
   * Get the attributes that should be cast.
   *
   * @return array<string, string>
   */
  protected function casts(): array
  {
    return [
      'email_verified_at' => 'datetime',
      'password' => 'hashed',
      'is_admin' => 'boolean',
    ];
  }

  protected static function boot(): void
  {
    parent::boot();

    // Clear cache on model changes
    static::saved(function ($user) {
      Cache::forget("user:{$user->id}");
      Cache::forget("user:email:{$user->email}");
      Cache::forget('users:count:*');
    });

    static::deleted(function ($user) {
      Cache::forget("user:{$user->id}");
      Cache::forget("user:email:{$user->email}");
      Cache::forget('users:count:*');
    });
  }

  public function addresses(): HasMany
  {
    return $this->hasMany(Address::class);
  }

  public function getDefaultAddressAttribute(): ?Address
  {
    return $this->addresses()->where('is_default', true)->first();
  }

  public function getProfilePhotoUrlAttribute(): string
  {
    if (! empty($this->profile_photo_path)) {
      return Storage::url($this->profile_photo_path);
    }

    return 'https://ui-avatars.com/api/?name=' . urlencode($this->name ?? 'User') . '&color=047857&background=ecfdf5&bold=true&size=128';
  }

  public function orders(): HasMany
  {
    return $this->hasMany(Order::class);
  }

  public function cart(): HasOne
  {
    return $this->hasOne(Cart::class);
  }

  public function getFormattedMonthlySpending(): string
  {
    $monthlyTotal = $this->orders()
      ->where('status', Order::STATUS_COMPLETED)
      ->whereMonth('created_at', now()->month)
      ->whereYear('created_at', now()->year)
      ->sum('total_amount');

    return 'Rp' . number_format($monthlyTotal, 0, ',', '.');
  }
}
