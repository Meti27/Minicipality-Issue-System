<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComplaintStatusHistory extends Model
{
    protected $fillable = [
        'complaint_id',
        'changed_by',
        'old_status',
        'new_status',
        'comment',
    ];

    public function complaint()
    {
        return $this->belongsTo(Complaint::class);
    }

    public function changedBy()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
