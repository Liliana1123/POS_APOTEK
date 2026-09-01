<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    public $timestamps = false; // only created_at is used

    protected $fillable = [
        'user_id',
        'user_name',
        'action',
        'target',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Log a user activity.
     */
    public static function log(string $action, ?string $target = null): self
    {
        $user = auth()->user();
        return self::create([
            'user_id' => $user ? $user->id : null,
            'user_name' => $user ? $user->name : 'System',
            'action' => $action,
            'target' => $target,
            'created_at' => now(),
        ]);
    }
}
