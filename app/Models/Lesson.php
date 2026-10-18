<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Lesson extends Model
{
    use Commentable;
    protected $fillable = [
        'course_id',
        'title',
        'description',
        'video_url',
        'sort_order',
    ];

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
