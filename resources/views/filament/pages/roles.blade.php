<x-filament-panels::page>
    @php
        $roles    = $this->getRoles();
        $allPerms = $this->getAllPermissions();
        $permMeta = $this->getPermissionMeta();
    @endphp

    <div class="space-y-6">

        {{-- Permissions matrix --}}
        <x-filament::section>
            <x-slot name="heading">Permissions Matrix</x-slot>
            <x-slot name="description">Which permissions are assigned to each role.</x-slot>

            <div class="overflow-x-auto">
                <table class="w-full divide-y divide-gray-200 dark:divide-white/5 text-sm">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-white/5">
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                                Permission
                            </th>
                            @foreach($roles as $role)
                            <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                                {{ str_replace('_', ' ', $role->name) }}
                            </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                        @foreach($allPerms as $permission)
                        @php $pm = $permMeta[$permission->name] ?? ['label' => $permission->name, 'group' => 'Other']; @endphp
                        <tr class="hover:bg-gray-50 dark:hover:bg-white/5">
                            <td class="px-4 py-3 text-gray-950 dark:text-white font-medium">
                                <div>
                                    {{ $pm['label'] }}
                                    <span class="ml-1.5 text-xs text-gray-400 dark:text-gray-500">
                                        {{ $pm['group'] }}
                                    </span>
                                </div>
                            </td>
                            @foreach($roles as $role)
                            @php $has = $role->permissions->contains('name', $permission->name); @endphp
                            <td class="px-4 py-3 text-center">
                                @if($has)
                                    <x-filament::badge color="success">Yes</x-filament::badge>
                                @else
                                    <x-filament::badge color="gray">No</x-filament::badge>
                                @endif
                            </td>
                            @endforeach
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-filament::section>

        {{-- Role details --}}
        @foreach($roles as $role)
        @php $permMeta = $this->getPermissionMeta(); @endphp
        <x-filament::section>
            <x-slot name="heading">
                {{ ucwords(str_replace('_', ' ', $role->name)) }}
                <span class="ml-2 text-sm font-normal text-gray-400">({{ $role->users->count() }} {{ $role->users->count() === 1 ? 'user' : 'users' }})</span>
            </x-slot>
            <x-slot name="description">
                {{ $this->getRoleMeta()[$role->name]['description'] ?? '' }}
            </x-slot>

            <div class="space-y-4">

                {{-- Permissions --}}
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400 mb-2">
                        Assigned Permissions ({{ $role->permissions->count() }})
                    </p>
                    @if($role->permissions->isEmpty())
                        <p class="text-sm text-gray-400 dark:text-gray-500 italic">No permissions assigned.</p>
                    @else
                    <div class="flex flex-wrap gap-2">
                        @foreach($role->permissions as $perm)
                        @php $pm = $this->getPermissionMeta()[$perm->name] ?? ['label' => $perm->name]; @endphp
                        <x-filament::badge color="primary">{{ $pm['label'] }}</x-filament::badge>
                        @endforeach
                    </div>
                    @endif
                </div>

                {{-- Users --}}
                @if($role->users->isNotEmpty())
                <div class="pt-3 border-t border-gray-100 dark:border-white/5">
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400 mb-2">
                        Users with this role
                    </p>
                    <div class="flex flex-wrap gap-2">
                        @foreach($role->users as $user)
                        <x-filament::badge color="gray">{{ $user->name }}</x-filament::badge>
                        @endforeach
                    </div>
                </div>
                @endif

            </div>
        </x-filament::section>
        @endforeach

        {{-- Note --}}
        <x-filament::section>
            <p class="text-sm text-gray-500 dark:text-gray-400">
                This page is read-only. Role permissions are fixed in the system.
                To control which pages a specific user can access, go to
                <a href="{{ route('filament.admin.pages.user-permissions-page') }}"
                   class="font-medium text-primary-600 hover:underline dark:text-primary-400">
                    Page Permissions
                </a>.
            </p>
        </x-filament::section>

    </div>
</x-filament-panels::page>
