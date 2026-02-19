<?php

namespace App\Models;

use App\TimeEntryStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimeEntries extends Model
{
    use HasFactory;

    protected $table = "time_entries";
    protected $primaryKey = 'id';

    protected $fillable = [
        "user_id",
        "clock_in_at",
        "clock_out_at",
    ];

    protected function casts()
    {
        return [
            "user_id"=>"integer",
            "clock_in_at"=>"datetime",
            "clock_out_at"=>"datetime",
        ];
    }

    public function user(){
        return $this->belongsTo(User::class, "user_id","id");
    }
}
