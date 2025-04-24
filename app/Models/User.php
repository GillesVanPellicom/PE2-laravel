<?php

namespace App\Models;

use App\Helpers\ConsoleHelper;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail {
  /** @use HasFactory<\Database\Factories\UserFactory> */
  use HasFactory, Notifiable, HasRoles, HasApiTokens;

  /**
   * The attributes that are mass assignable.
   *
   * @var list<string>
   */
  protected $fillable = [
    'first_name',
    'last_name',
    'is_company',
    'company_name',
    'VAT_Number',
    'email',
    'phone_number',
    'birth_date',
    'address_id',
    'password',
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
   * Get the attributes that should be cast.
   *
   * @return array<string, string>
   */
  protected function casts(): array {
    return [
      'email_verified_at' => 'datetime',
      'password' => 'hashed',
    ];
  }

  public function address(): BelongsTo {
    return $this->belongsTo(Address::class);
  }

  public function packages(): HasMany {
    return $this->hasMany(Package::class, 'user_id');
  }

  public function employee(): HasOne {
    return $this->hasOne(Employee::class, 'user_id');
  }

  public function sendMail(string $subject, string $blade): void {
    $addr = $this->getAttribute('email');

    Mail::send($blade, [], function ($message) use ($subject, $addr) {
      $message->to($addr)->subject($subject);
    });
  }
}
