<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Log setiap proses penarikan data eksternal (FR-04).
 */
class ApiFetchLog extends Model
{
    protected $fillable = [
        'source_type',
        'source_name',
        'status',
        'records_fetched',
        'message',
        'started_at',
        'finished_at',
    ];

    protected $casts = [
        'started_at'  => 'datetime',
        'finished_at' => 'datetime',
    ];

    /**
     * Mulai mencatat sebuah proses penarikan.
     */
    public static function start(string $type, string $name): self
    {
        return self::create([
            'source_type' => $type,
            'source_name' => $name,
            'status'      => 'running',
            'started_at'  => now(),
        ]);
    }

    /**
     * Tandai proses selesai dengan status tertentu.
     */
    public function finish(string $status, int $records = 0, ?string $message = null): void
    {
        $this->update([
            'status'          => $status,
            'records_fetched' => $records,
            'message'         => $message,
            'finished_at'     => now(),
        ]);
    }
}
