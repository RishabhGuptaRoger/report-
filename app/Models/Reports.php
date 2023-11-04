<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reports extends Model
{
    use HasFactory;
    protected $connection = 'mongodb';
    protected $collection = 'all_reports_daily';

    protected $fillable = [
        'integration_id',
        'integration_data',
        'stats',
    ];

}
