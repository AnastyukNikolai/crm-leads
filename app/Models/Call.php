<?php

namespace App\Models;

use App\Enums\CallResult;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Call extends Model
{
    use HasFactory;

    protected $fillable = [
        'lead_id',
        'manager_id',
        'duration',
        'result',
    ];

    protected function casts(): array
    {
        return [
            'result' => CallResult::class,
        ];
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(Manager::class);
    }
}
