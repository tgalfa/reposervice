<?php

namespace tgalfa\RepoService\Tests\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use tgalfa\RepoService\Tests\Factories\TestModelFactory;

class TestModel extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'type'];

    /**
     * Set Model's Factory class.
     *
     * @return \tgalfa\RepoService\Tests\Factories\TestModelFactory
     */
    protected static function newFactory()
    {
        return TestModelFactory::new();
    }

    /**
     * Scope a query to filter with Test type.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeTestType(Builder $query)
    {
        return $query->byType('test');
    }

    /**
     * Scope a query to filter with type.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $type
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByType(Builder $query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope a query to filter with Name string.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $substring
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNameStarts(Builder $query, string $substring)
    {
        return $query->where('name', 'like', "{$substring}%");
    }
}
