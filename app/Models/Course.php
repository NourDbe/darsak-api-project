
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use App\Traits\Commentable;

class Course extends Model
{
    use Commentable;
    protected $fillable = [
        'teacher_id',
        'title',
        'description',
        'level',
        'status',
        'is_free',
        'price',
    ];

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }
    public function lessons(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
    return $this->hasMany(Lesson::class);
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
