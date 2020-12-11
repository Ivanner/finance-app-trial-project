<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ImportProcess
 * @package App\Models
 *
 * @property int $id
 * @property int $user_id
 * @property string $fileName
 * @property bool $imported
 * @property int $rowsImported
 * @property int $totalRows
 */
class ImportProcess extends Model
{
    use HasFactory;

    protected $fillable = [
        'fileName'
    ];
}
