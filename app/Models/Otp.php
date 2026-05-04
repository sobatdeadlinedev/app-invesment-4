<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Otp extends Model
{
    use HasFactory;

    protected $table = 'otp';

    public const PURPOSE_REGISTER = 'register';
    public const PURPOSE_RESET_PASSWORD = 'reset_password';

    protected $fillable = [
        'email',
        'purpose',
        'otp',
        'is_used',
        'expired_at',
    ];

    protected $casts = [
        'expired_at' => 'datetime',
        'is_used' => 'boolean',
    ];

    /**
     * Generate random 6 digit OTP
     */
    public static function generate(): string
    {
        return str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }
}
