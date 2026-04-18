<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\CatatLogAktivitas;

class Pengaturan extends Model
{
    protected $guarded = [];
    use CatatLogAktivitas;
}
