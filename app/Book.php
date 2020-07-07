<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Book extends Model
{
    protected $dates = [
        'release_date',
    ];

    /*
     * Relationships
     */

    /**
     * Authors of a book
     * @return BelongsToMany
     */
    public function authors(): BelongsToMany
    {
        return $this->belongsToMany(Author::class)
            ->withTimestamps();
    }
}
