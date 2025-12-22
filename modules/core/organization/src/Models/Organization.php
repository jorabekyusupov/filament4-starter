<?php

namespace Modules\Organization\Models;

use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Organization\Policies\OrganizationPolicy;

#[UsePolicy(OrganizationPolicy::class)]
class Organization extends Model
{
    use SoftDeletes;

    public const SLUG_DEFAULT = 'default';
    public const SLUG_CA = 'ca';

    protected $fillable = [
        'slug',
        'structure_id',
        'hidden',
        'name',
    ];

    protected function casts(): array
    {
        return [
            'name' => 'array',
            'hidden' => 'boolean',
        ];
    }



    public function scopeDefaultId(): ?int
    {
        return $this->where('slug', 'default')->value('id');
    }

    public function scopeWithoutHidden()
    {
        return $this->where('hidden', false);
    }

    public function users()
    {
        return $this->hasMany(\Modules\User\Models\User::class);
    }

}
