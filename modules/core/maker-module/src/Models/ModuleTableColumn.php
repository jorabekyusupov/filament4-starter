<?php

namespace Modules\MakerModule\Models;

use Illuminate\Database\Eloquent\Model;

class ModuleTableColumn extends Model
{
    protected $table = 'module_table_columns';
    protected $fillable = [
        'module_id',
        'module_table_id',
        'name',
        'type',
        'options',
        'nullable',
        'unique',
        'index',
        'foreign',
        'foreign_table',
        'foreign_column',
        'on_delete',
        'on_update',
        'status',
        'user_id',
    ];

    protected $casts = [
        'options' => 'array',
        'nullable' => 'boolean',
        'unique' => 'boolean',
        'index' => 'boolean',
        'foreign' => 'boolean',
    ];

    public function table()
    {
        return $this->belongsTo(ModuleTable::class, 'module_table_id');
    }

    public function module()
    {
        return $this->belongsTo(Module::class, 'module_id');
    }
}
