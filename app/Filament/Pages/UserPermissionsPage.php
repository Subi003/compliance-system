<?php

namespace App\Filament\Pages;

use App\Models\User;
use App\Models\UserPagePermission;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class UserPermissionsPage extends Page
{
    protected string $view = 'filament.pages.user-permissions';

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedShieldCheck;

    protected static string|\UnitEnum|null $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 10;

    protected static ?string $navigationLabel = 'Page Permissions';

    protected static ?string $title = 'User Page Permissions';

    /** Currently selected user ID */
    public ?int $selectedUserId = null;

    /**
     * Permissions map: resource_slug => ['can_view' => bool, 'can_edit' => bool]
     */
    public array $permissions = [];

    public function mount(): void
    {
        $first = User::orderBy('name')->first();
        if ($first) {
            $this->selectedUserId = $first->id;
            $this->loadPermissions();
        }
    }

    public function updatedSelectedUserId(): void
    {
        $this->loadPermissions();
    }

    public function loadPermissions(): void
    {
        if (! $this->selectedUserId) {
            $this->permissions = [];
            return;
        }

        // Load saved permissions keyed by resource
        $saved = UserPagePermission::where('user_id', $this->selectedUserId)
            ->get()
            ->keyBy('resource');

        $this->permissions = [];
        foreach (UserPagePermission::allResources() as $slug => $label) {
            $record = $saved->get($slug);
            $this->permissions[$slug] = [
                'can_view' => (bool) ($record?->can_view ?? true),
                'can_edit' => (bool) ($record?->can_edit ?? true),
            ];
        }
    }

    /**
     * When can_view is unchecked, also force can_edit off.
     */
    public function updatedPermissions(mixed $value, string $key): void
    {
        // $key looks like "branches.can_view" or "branches.can_edit"
        [$slug, $field] = explode('.', $key);

        if ($field === 'can_view' && ! $value) {
            // Disable edit too when view is revoked
            $this->permissions[$slug]['can_edit'] = false;
        }

        if ($field === 'can_edit' && $value) {
            // Re-enable view when edit is enabled
            $this->permissions[$slug]['can_view'] = true;
        }
    }

    public function save(): void
    {
        if (! $this->selectedUserId) {
            return;
        }

        foreach ($this->permissions as $resource => $perms) {
            $canView = (bool) ($perms['can_view'] ?? true);
            $canEdit = (bool) ($perms['can_edit'] ?? true);

            // Edit can't be true if view is false
            if (! $canView) {
                $canEdit = false;
            }

            UserPagePermission::updateOrCreate(
                ['user_id' => $this->selectedUserId, 'resource' => $resource],
                ['can_view' => $canView, 'can_edit' => $canEdit]
            );
        }

        Notification::make()
            ->title('Permissions saved successfully')
            ->success()
            ->send();
    }

    public function getUsers(): array
    {
        return User::with('roles')->orderBy('name')->get()
            ->mapWithKeys(fn (User $u) => [
                $u->id => $u->name . ' — ' . ($u->roles->first()?->name ?? 'no role'),
            ])
            ->toArray();
    }

    public function getSelectedUser(): ?User
    {
        return $this->selectedUserId
            ? User::with('roles', 'branches')->find($this->selectedUserId)
            : null;
    }

    public function getResourceLabels(): array
    {
        return UserPagePermission::allResources();
    }

    public function getResourceIcons(): array
    {
        return [
            'branches'           => 'heroicon-o-building-office-2',
            'companies'          => 'heroicon-o-building-office',
            'compliances'        => 'heroicon-o-clipboard-document-check',
            'compliance-types'   => 'heroicon-o-tag',
            'compliance-records' => 'heroicon-o-document-text',
            'users'              => 'heroicon-o-users',
        ];
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->hasRole('admin') ?? false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canAccess();
    }
}
