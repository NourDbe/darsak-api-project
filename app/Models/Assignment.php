<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use App\Traits\Commentable;

class Assignment extends Model
{
    use Commentable;
    protected $fillable = [
        'course_id',
        'title',
        'description',
        'due_at',
    ];

    protected function casts(): array
    {
        return [
            'due_at' => 'datetime',
        ];
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }
    public function image(): MorphOne
    {
    return $this->morphOne(Image::class, 'imageable');
    }
    public function categories(): MorphToMany
    {
    return $this->morphToMany(Category::class, 'categorizable');
    }
}
