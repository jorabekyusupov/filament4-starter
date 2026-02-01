<?php

namespace Modules\User\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Modules\Organization\Models\Organization;
use Modules\User\Policies\UserPolicy;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasRoles;

#[UsePolicy(UserPolicy::class)]
class User extends Authenticatable implements FilamentUser, HasAvatar
{
    /** @use HasFactory<\Modules\User\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles, SoftDeletes, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll();
    }

    protected static function newFactory()
    {
        return \Modules\User\Factories\UserFactory::new();
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */

    protected $fillable = [
        'name',
        'first_name',
        'last_name',
        'middle_name',
        'email',
        'password',
        'organization_id',
        'type',
        'username',
        'pin',
        'status',
        'created_by',
        'updated_by',
        'deleted_by',
        'photo_base64',
        'photo_mime',
        'photo_path',
        'tg_id',
        'dont_touch'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'status' => 'boolean',
            'dont_touch' => 'boolean',
        ];
    }

    //creating boot
    protected static function booted(): void
    {
        static::creating(function (User $user) {
            $user->name = "{$user->first_name} {$user->last_name}";
        });
        static::updating(function (User $user) {
            if ($user->isDirty(['first_name', 'last_name'])) {
                $user->name = "{$user->first_name} {$user->last_name}";
            }
        });

        static::saving(function (User $user) {
            if ($user->isDirty('photo_path')) {
                if ($user->photo_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($user->photo_path)) {
                    $content = \Illuminate\Support\Facades\Storage::disk('public')->get($user->photo_path);
                    $mime = \Illuminate\Support\Facades\Storage::disk('public')->mimeType($user->photo_path);
                    $user->photo_base64 = base64_encode($content);
                    $user->photo_mime = $mime;
                } else {
                    $user->photo_base64 = null;
                    $user->photo_mime = null;
                }
            }
        });

    }


    public function canAccessPanel(Panel $panel): bool
    {
        $hasRole = $this->hasRole(Role::all());
        $status = $this->status;
        $hasOrg = $this->organization()->exists();
        $orgStatus = $this->organization->status ?? false;

        return $hasRole && $status && $hasOrg && $orgStatus;
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function defOrganization()
    {
        return $this->belongsTo(Organization::class)
            ->where('slug', Organization::SLUG_DEFAULT);
    }

    public function caOrganization()
    {
        return $this->belongsTo(Organization::class, 'organization_id')
            ->where('slug', Organization::SLUG_CA);
    }

    public function hasSuperAdmin(): bool
    {
        return $this->type === 'superadmin';
    }

    public function hasDefaultOrg(): bool
    {
        //check relation
        return $this->defOrganization()->exists();
    }

    public function hasCAOrganization(): bool
    {
        return $this->caOrganization()->exists();
    }

    public function hasRoleSuperAdmin(): bool
    {

        return $this->hasRole('super_admin');
    }

    public function hasAdmin(): bool
    {

        return $this->hasRole('Admin');
    }

    #[Scope]
    protected function hasCAOrg(Builder $query): void
    {
        $query->whereHas('caOrganization');
    }

    public function firstRole()
    {
        return $this->roles()->first();
    }

    public function getFilamentName(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }
    public function getFilamentAvatarUrl(): ?string
    {

        if ($this->photo_base64 && $this->photo_mime) {
            return "data:{$this->photo_mime};base64,{$this->photo_base64}";
        }

        if ($this->photo_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($this->photo_path)) {
            $content = \Illuminate\Support\Facades\Storage::disk('public')->get($this->photo_path);
            $mime = \Illuminate\Support\Facades\Storage::disk('public')->mimeType($this->photo_path);
            $base64 = base64_encode($content);
            return "data:{$mime};base64,{$base64}";
        }
        return null;
    }

}
