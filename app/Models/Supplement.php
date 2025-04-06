<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplement extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'name', 'form', 'reason', 'frequency',
        'time', 'duration', 'refill_reminder', 'instructions', 'icon'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reminders()
    {
        return $this->hasMany(Reminder::class);
    }
}
