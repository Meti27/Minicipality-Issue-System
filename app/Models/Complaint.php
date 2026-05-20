<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Complaint extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'description',
        'location',
        'image_path',
        'status',
        'priority',
        'rejection_reason',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function statusHistories()
    {
        return $this->hasMany(ComplaintStatusHistory::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }
}
