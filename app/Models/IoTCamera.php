<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IoTCamera extends Model
{
    use HasFactory;

    protected $table = 'iot_cameras';

    protected $fillable = [
        'id_device',
        'message',
        'image',
    ];
}