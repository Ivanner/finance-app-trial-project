<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Transaction
 * @package App\Models
 *
 * @property int $id
 * @property int $user_id
 * @property string $label
 * @property float $amount
 * @property Carbon $date
 * @property int import_process_id
 *
 */
class Transaction extends Model
{
    use HasFactory;

    protected $casts = [
        'user_id' => 'int',
        'amount' => 'float',
    ];

    protected $dates = [
        'date'
    ];

    protected $fillable = [
        'label',
        'amount',
        'date'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
