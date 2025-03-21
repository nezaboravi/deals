<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class Deal extends Model
{
    use HasFactory;

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected function casts(): array
    {
        return [
            'expiry_date' => 'date',
            'published_at' => 'datetime',
            'is_featured' => 'boolean',
            'is_verified' => 'boolean',
        ];
    }

    /**
     * Get the category that owns the deal.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
    
    /**
     * Get the category name, safely handling null categories.
     */
    public function getCategoryNameAttribute(): string
    {
        return $this->category?->name ?? 'Uncategorized';
    }
    
    /**
     * Get the category slug, safely handling null categories.
     */
    public function getCategorySlugAttribute(): ?string
    {
        return $this->category?->slug;
    }
    
    /**
     * Get the category color, safely handling null categories.
     */
    public function getCategoryColorAttribute(): string
    {
        return $this->category?->color ?? 'gray';
    }

    /**
     * Get the user that owns the deal.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the votes for the deal.
     */
    public function votes(): HasMany
    {
        return $this->hasMany(Vote::class);
    }

    /**
     * Set the title attribute and automatically generate a slug.
     */
    protected function setTitleAttribute(string $value): void
    {
        $this->attributes['title'] = $value;
        $this->attributes['slug'] = Str::slug($value);
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Scope a query to only include published deals.
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->whereNotNull('published_at');
    }

    /**
     * Scope a query to only include featured deals.
     */
    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope a query to only include deals that haven't expired.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('expiry_date', '>=', now()->format('Y-m-d'));
    }

    /**
     * Scope a query to order deals by popularity (vote count).
     */
    public function scopePopular(Builder $query): Builder
    {
        return $query->orderByDesc('vote_count');
    }

    /**
     * Scope a query to order deals by recency.
     */
    public function scopeRecent(Builder $query): Builder
    {
        return $query->orderByDesc('published_at');
    }
}
