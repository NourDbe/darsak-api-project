<?php

namespace App\Models;

use App\Traits\Commentable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use App\Models\Scopes\PublishedCourseScope;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Course extends Model
{
    use Commentable;
    use HasFactory;

   protected static function booted(): void
{
    static::addGlobalScope(new PublishedCourseScope());
}

    protected $fillable = [
        'teacher_id',
        'title',
        'description',
        'level',
        'status',
        'is_free',
        'price',
    ];

    /**
     * الكورس يتبع لمدرّس واحد.
     */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }
    /**
 * الطلاب المسجلون في الكورس.
 */
public function students(): BelongsToMany
{
    return $this->belongsToMany(User::class, 'course_student', 'course_id', 'student_id')
        ->withTimestamps();
}

    /**
     * الكورس يحتوي على عدة دروس.
     */
    public function lessons(): HasMany
    {
        return $this->hasMany(Lesson::class);
    }

    /**
     * الكورس يمتلك صورة واحدة.
     */
    public function image(): MorphOne
    {
        return $this->morphOne(Image::class, 'imageable');
    }

    /**
     * الكورس يمكن أن يرتبط بعدة تصنيفات.
     */
    public function categories(): MorphToMany
    {
        return $this->morphToMany(Category::class, 'categorizable');
    }

    /**
     * الكورس يحتوي على عدة واجبات.
     */
    public function assignments(): HasMany
    {
        return $this->hasMany(Assignment::class);
    }

    /**
     * إرجاع الدورات المنشورة فقط.
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published');
    }

    /**
     * إرجاع الدورات المجانية فقط.
     */
    public function scopeFree(Builder $query): Builder
    {
        return $query->where('is_free', true);
    }

    /**
     * إرجاع الدورات حسب المستوى.
     */
    public function scopeForLevel(
        Builder $query,
        string $level
    ): Builder {
        return $query->where('level', $level);
    }
}
