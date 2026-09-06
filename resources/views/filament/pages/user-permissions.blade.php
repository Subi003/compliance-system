<x-filament-panels::page>
    <div class="space-y-6">

        {{-- User selector --}}
        <x-filament::section>
            <x-slot name="heading">Select User</x-slot>
            <x-slot name="description">Choose a user to manage their page-level access permissions.</x-slot>

            <div class="max-w-sm">
                <x-filament::input.wrapper>
                    <select
                        wire:model.live="selectedUserId"
                        class="block w-full border-0 bg-transparent py-1.5 pe-8 text-gray-950 placeholder:text-gray-400 focus:ring-0 dark:text-white sm:text-sm sm:leading-6"
                    >
                        @foreach($this->getUsers() as $id => $label)
                            <option value="{{ $id }}" @selected((int)$selectedUserId === (int)$id)>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </x-filament::input.wrapper>
            </div>
        </x-filament::section>

        @if($selectedUserId && count($permissions))

        {{-- Permissions table --}}
        <x-filament::section>
            <x-slot name="heading">Page Access Permissions</x-slot>
            <x-slot name="description">
                <strong>View</strong> — user can see the page in the sidebar and open it.
                &nbsp;·&nbsp;
                <strong>Edit</strong> — user can create, update and delete records.
                Disabling View automatically disables Edit.
            </x-slot>

            <div class="overflow-x-auto">
                <table class="w-full divide-y divide-gray-200 dark:divide-white/5 text-sm">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-white/5">
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                                Page / Resource
                            </th>
                            <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400 w-28">
                                View
                            </th>
                            <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400 w-28">
                                Edit
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400 w-36">
                                Access Level
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                        @foreach($this->getResourceLabels() as $slug => $label)
                        @php
                            $canView = $permissions[$slug]['can_view'] ?? true;
                            $canEdit = $permissions[$slug]['can_edit'] ?? true;
                        @endphp
                        <tr class="hover:bg-gray-50 dark:hover:bg-white/5">
                            <td class="px-4 py-3 font-medium text-gray-950 dark:text-white">
                                {{ $label }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                <input
                                    type="checkbox"
                                    wire:model.live="permissions.{{ $slug }}.can_view"
                                    class="rounded border-gray-300 text-primary-600 shadow-sm focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 cursor-pointer"
                                    @checked($canView)
                                >
                            </td>
                            <td class="px-4 py-3 text-center">
                                <input
                                    type="checkbox"
                                    wire:model.live="permissions.{{ $slug }}.can_edit"
                                    class="rounded border-gray-300 text-primary-600 shadow-sm focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 {{ $canView ? 'cursor-pointer' : 'cursor-not-allowed opacity-40' }}"
                                    @checked($canEdit && $canView)
                                    @disabled(!$canView)
                                >
                            </td>
                            <td class="px-4 py-3">
                                @if(!$canView)
                                    <x-filament::badge color="danger">No Access</x-filament::badge>
                                @elseif($canEdit)
                                    <x-filament::badge color="success">Full Access</x-filament::badge>
                                @else
                                    <x-filament::badge color="warning">View Only</x-filament::badge>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4 flex items-center gap-3 pt-4 border-t border-gray-100 dark:border-white/5">
                <x-filament::button
                    wire:click="save"
                    wire:loading.attr="disabled"
                >
                    Save Permissions
                </x-filament::button>
                <span class="text-sm text-gray-500 dark:text-gray-400">
                    Changes apply on the user's next page load.
                </span>
            </div>

        </x-filament::section>

        @endif
    </div>
</x-filament-panels::page>
