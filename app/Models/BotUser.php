<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BotUser extends Model
{
    protected $fillable = [
        'tenant_id',
        'telegram_id',
        'first_name',
        'last_name',
        'username',
        'phone',
        'language_code',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
