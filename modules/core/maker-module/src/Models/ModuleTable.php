<?php

namespace Modules\MakerModule\Models;

use Modules\App\Models\BaseModel;

class ModuleTable extends BaseModel
{
    protected $table = 'module_tables';
    protected $fillable = [
        'name',
        'module_id',
        'soft_deletes',
        'timestamps',
        'logged',
        'status',
        'user_id',
    ];

    protected $casts = [
        'soft_deletes' => 'boolean',
        'timestamps' => 'boolean',
        'logged' => 'boolean',
    ];

    public function columns()
    {
        return $this->hasMany(ModuleTableColumn::class, 'module_table_id');
    }

    public function module()
    {
        return $this->belongsTo(Module::class, 'module_id');
    }
}

