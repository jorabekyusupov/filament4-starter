<?php

namespace Tests\Feature;

use Modules\RolePermission\Models\Permission;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class PermissionObserverTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_automatically_fills_module_and_group_for_core_language_model()
    {
        // Permission name usually: 'action_model' or 'action_models'
        // derived name for 'view_languages' -> 'Language'
        // Language model is at modules/core/language/src/Models/Language.php

        $permission = Permission::create(['name' => 'view_languages', 'guard_name' => 'web']);

        $this->assertEquals('system', $permission->module);
        $this->assertEquals('language', $permission->group);
    }

    #[Test]
    public function it_automatically_fills_module_and_group_for_core_user_model()
    {
        // derived name for 'edit_users' -> 'User'
        // User model is at modules/core/user/src/Models/User.php

        $permission = Permission::create(['name' => 'edit_users', 'guard_name' => 'web']);

        $this->assertEquals('system', $permission->module);
        $this->assertEquals('user', $permission->group);
    }

    #[Test]
    public function it_does_not_overwrite_existing_module_and_group()
    {
        $permission = Permission::create([
            'name' => 'view_languages',
            'guard_name' => 'web',
            'module' => 'custom_module',
            'group' => 'custom_group'
        ]);

        $this->assertEquals('custom_module', $permission->module);
        $this->assertEquals('custom_group', $permission->group);
    }
}
