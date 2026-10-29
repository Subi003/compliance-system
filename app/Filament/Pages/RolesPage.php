<?php

namespace App\Filament\Pages;

use App\Models\User;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesPage extends Page
{
    protected string $view = 'filament.pages.roles';

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedIdentification;

    protected static string|\UnitEnum|null $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 9;

    protected static ?string $navigationLabel = 'Roles & Permissions';

    protected static ?string $title = 'Roles & Permissions';

    /** Only admins can view this page */
    public static function canAccess(): bool
    {
        return auth()->user()?->hasRole('admin') ?? false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canAccess();
    }

    /**
     * Returns all roles with their permissions and user counts.
     */
    public function getRoles(): \Illuminate\Support\Collection
    {
        return Role::with('permissions', 'users')->orderBy('name')->get();
    }

    /**
     * All permissions in the system.
     */
    public function getAllPermissions(): \Illuminate\Support\Collection
    {
        return Permission::orderBy('name')->get();
    }

    /**
     * Role metadata: colour and description.
     */
    public function getRoleMeta(): array
    {
        return [
            'admin' => [
                'color'       => '#ef4444',
                'bg'          => '#fef2f2',
                'border'      => '#fecaca',
                'description' => 'Full system access. Can manage all resources, users, roles and permissions.',
                'badge_bg'    => '#fca5a5',
                'badge_text'  => '#7f1d1d',
            ],
            'branch_manager' => [
                'color'       => '#f59e0b',
                'bg'          => '#fffbeb',
                'border'      => '#fde68a',
                'description' => 'Manages compliance activities for assigned branches. Can approve/reject records.',
                'badge_bg'    => '#fcd34d',
                'badge_text'  => '#78350f',
            ],
            'compliance_officer' => [
                'color'       => '#3b82f6',
                'bg'          => '#eff6ff',
                'border'      => '#bfdbfe',
                'description' => 'Creates and tracks compliance records for their assigned branches.',
                'badge_bg'    => '#93c5fd',
                'badge_text'  => '#1e3a8a',
            ],
            'viewer' => [
                'color'       => '#6b7280',
                'bg'          => '#f9fafb',
                'border'      => '#e5e7eb',
                'description' => 'Read-only access to the dashboard and reports. No editing allowed.',
                'badge_bg'    => '#d1d5db',
                'badge_text'  => '#1f2937',
            ],
        ];
    }

    /**
     * Permission display names and their category grouping.
     */
    public function getPermissionMeta(): array
    {
        return [
            'view dashboard'             => ['label' => 'View Dashboard',           'group' => 'Dashboard'],
            'manage users'               => ['label' => 'Manage Users',              'group' => 'Admin'],
            'manage branches'            => ['label' => 'Manage Branches',           'group' => 'Operations'],
            'manage compliances'         => ['label' => 'Manage Compliances',        'group' => 'Operations'],
            'manage compliance records'  => ['label' => 'Manage Compliance Records', 'group' => 'Operations'],
            'view reports'               => ['label' => 'View Reports',              'group' => 'Dashboard'],
        ];
    }
}
