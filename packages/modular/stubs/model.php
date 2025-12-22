<?php

namespace StubModuleNamespace\StubSubModulePrefix\Models;


use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Model;
use StubModuleNamespace\StubSubModulePrefix\Policies\StubPolicyClass;

#[UsePolicy(StubPolicyClass::class)]
class StubTableName extends Model
{
    useSoftDeletes
    protected $table = 'table_name';
    protected $fillable = [
        'fillables',
    ];

    protected $casts = [
        castsContent
    ];

}