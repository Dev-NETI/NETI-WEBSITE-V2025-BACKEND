<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class News extends Model
{
    protected $table = 'news';
    
    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'content',
        'author',
        'author_title',
        'date',
        'image',
        'featured',
        'status',
        'views',
        'created_by',
    ];

    protected $casts = [
        'date' => 'date',
        'featured' => 'boolean',
        'views' => 'integer',
    ];

    protected $appends = ['image_url'];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getImageUrlAttribute(): ?string
    {
        if ($this->image) {
            return asset('storage/news_images/' . $this->image);
        }
        return null;
    }

    public static function boot()
    {
        parent::boot();
        
        static::creating(function ($news) {
            if (empty($news->slug)) {
                $news->slug = Str::slug($news->title);
            }
        });
        
        static::updating(function ($news) {
            if ($news->isDirty('title') && empty($news->slug)) {
                $news->slug = Str::slug($news->title);
            }
        });
    }

    public function incrementViews(): void
    {
        $this->increment('views');
    }
}
