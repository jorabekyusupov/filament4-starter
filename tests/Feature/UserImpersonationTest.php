<?php

namespace Tests\Feature;

use Modules\User\Models\User;
use Modules\Organization\Models\Organization;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\User\Filament\Resources\UserResource;
use Filament\Facades\Filament;

class UserImpersonationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Manually register resource to ensure it is available in the admin panel
        // Note: We might need to ensure we are registering to the right panel if multiple exist
        // But defaults often work or we can target 'admin'
        try {
            Filament::registerResources([UserResource::class]);
        } catch (\Exception $e) {
            // Panel might not be current, but let's try
        }
    }

    #[Test]
    public function super_admin_can_impersonate_user()
    {
        $org = Organization::create([
            'name' => ['en' => 'Test Org', 'ru' => 'Test Org', 'uz' => 'Test Org'],
            'slug' => 'test-org',
            'status' => true
        ]);

        $role = Role::create(['name' => 'Super Admin', 'guard_name' => 'web']);
        $permission = Permission::create(['name' => 'ViewAny:User', 'guard_name' => 'web']);
        $role->givePermissionTo($permission);

        $superAdmin = User::factory()->create([
            'type' => 'superadmin',
            'email' => 'admin@admin.com',
            'organization_id' => $org->id,
            'status' => true,
        ]);

        $superAdmin->assignRole($role);

        $targetUser = User::factory()->create([
            'type' => 'employee',
            'email' => 'target@user.com',
            'organization_id' => $org->id,
            'status' => true,
        ]);
        $targetUser->assignRole($role);

        $this->actingAs($superAdmin);

        \Livewire\Livewire::test(UserResource\Pages\ListUsers::class)
            ->assertSuccessful()
            ->assertTableActionVisible('login', $targetUser)
            ->mountTableAction('login', $targetUser)
            ->callMountedTableAction()
            ->assertRedirect('/admin');
    }
}
