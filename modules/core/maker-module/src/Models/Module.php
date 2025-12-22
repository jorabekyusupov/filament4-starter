<?php

namespace Modules\MakerModule\Models;

use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Model;
use Jora\Modular\Console\Commands\Make\MakeModule;
use Modules\MakerModule\MakerModulePolicy;
use Modules\User\Models\User;


#[UsePolicy(MakerModulePolicy::class)]
class Module extends Model
{
    protected $table = 'modules';

    protected $fillable = [
        'name',
        'namespace',
        'path',
        'user_id',
        'status',
        'stable',
    ];

    // Add any relationships or methods as needed

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tables()
    {
        return $this->hasMany(ModuleTable::class, 'module_id');
    }

    public function scopeGetPath($path = null)
    {
        return base_path($this->path . ($path ? '/' . $path : ''));
    }

    public function scopeIsActive()
    {
        return $this->status;
    }

    public function scopeGetNamespace($namespace = null)
    {
        return $this->namespace . ($namespace ? '\\' . $namespace : '');
    }


}
