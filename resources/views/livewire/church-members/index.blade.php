<?php

use App\Models\ChurchMember;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Livewire\Volt\Component;

new #[Layout('components.layouts.app')] class extends Component {
    use WithPagination;
    use WithFileUploads;

    public $search = '';
    public $status = '';
    public $perPage = 10;
    public $sortField = 'name';
    public $sortDirection = 'asc';

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function with(): array
    {
        return [
            'members' => ChurchMember::query()
                ->when($this->search, function ($query) {
                    $query->where(function ($query) {
                        $query->where('name', 'like', '%' . $this->search . '%')
                            ->orWhere('email', 'like', '%' . $this->search . '%')
                            ->orWhere('telephone', 'like', '%' . $this->search . '%')
                            ->orWhere('home_town', 'like', '%' . $this->search . '%');
                    });
                })
                ->when($this->status, function ($query) {
                    $query->where('status', $this->status);
                })
                ->orderBy($this->sortField, $this->sortDirection)
                ->paginate($this->perPage),
        ];
    }
}; ?>

<div>
    <!-- Stats Overview -->
    <div class="mb-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-lg border bg-card p-4 shadow-sm">
            <div class="text-sm font-medium text-muted-foreground">Total Members</div>
            <div class="mt-2 text-2xl font-bold">{{ ChurchMember::count() }}</div>
        </div>
        <div class="rounded-lg border bg-card p-4 shadow-sm">
            <div class="text-sm font-medium text-muted-foreground">Active Members</div>
            <div class="mt-2 text-2xl font-bold text-green-600">
                {{ ChurchMember::where('status', 'active')->count() }}
            </div>
        </div>
        <div class="rounded-lg border bg-card p-4 shadow-sm">
            <div class="text-sm font-medium text-muted-foreground">Inactive Members</div>
            <div class="mt-2 text-2xl font-bold text-red-600">
                {{ ChurchMember::where('status', 'inactive')->count() }}
            </div>
        </div>
        <div class="rounded-lg border bg-card p-4 shadow-sm">
            <div class="text-sm font-medium text-muted-foreground">Pending Members</div>
            <div class="mt-2 text-2xl font-bold text-yellow-600">
                {{ ChurchMember::where('status', 'pending')->count() }}
            </div>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="mb-6 space-y-4 sm:flex sm:items-center sm:justify-between sm:space-y-0">
        <div class="flex flex-1 items-center gap-4">
            <div class="w-full max-w-sm">
                <label for="search" class="sr-only">Search members</label>
                <div class="relative">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                        <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <flux:input
                        wire:model.live="search"
                        id="search"
                        placeholder="Search by name, email, phone..."
                        type="search"
                        class="pl-10"
                    />
                </div>
            </div>

            <flux:select wire:model.live="status" class="max-w-xs">
                <option value="">All Status</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
                <option value="pending">Pending</option>
            </flux:select>
        </div>

        <div class="flex items-center gap-4">
            <flux:select wire:model.live="perPage" class="max-w-xs">
                <option value="10">10 per page</option>
                <option value="25">25 per page</option>
                <option value="50">50 per page</option>
                <option value="100">100 per page</option>
            </flux:select>

            <flux:button href="{{ route('church-members.create') }}" variant="primary" wire:navigate>
                <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Add Member
            </flux:button>
        </div>
    </div>

    <!-- Members Table -->
    <div class="rounded-lg border bg-card shadow-sm">
        <div class="relative w-full overflow-auto">
            <table class="w-full caption-bottom text-sm">
                <thead class="border-b bg-muted/50">
                    <tr>
                        <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground">
                            <button wire:click="sortBy('name')" class="flex items-center gap-2">
                                Photo & Name
                                @if($sortField === 'name')
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="{{ $sortDirection === 'asc'
                                                ? 'M8 15l4-4 4 4'
                                                : 'M8 9l4 4 4-4' }}" />
                                    </svg>
                                @endif
                            </button>
                        </th>
                        <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground">Contact Info</th>
                        <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground">
                            <button wire:click="sortBy('status')" class="flex items-center gap-2">
                                Status
                                @if($sortField === 'status')
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="{{ $sortDirection === 'asc'
                                                ? 'M8 15l4-4 4 4'
                                                : 'M8 9l4 4 4-4' }}" />
                                    </svg>
                                @endif
                            </button>
                        </th>
                        <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground">Actions</th>
                    </tr>
                </thead>
                <tbody class="[&_tr:last-child]:border-0">
                    @forelse($members as $member)
                        <tr class="border-b transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted">
                            <td class="p-4">
                                <div class="flex items-center gap-3">
                                    @if($member->photo_url)
                                        <img src="{{ $member->photo_url }}" alt="{{ $member->name }}"
                                            class="h-10 w-10 rounded-full object-cover" />
                                    @else
                                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-primary/10">
                                            <span class="text-sm font-medium text-primary">
                                                {{ substr($member->name, 0, 2) }}
                                            </span>
                                        </div>
                                    @endif
                                    <div>
                                        <div class="font-medium">{{ $member->name }}</div>
                                        <div class="text-sm text-muted-foreground">
                                            Member since {{ $member->created_at->format('M Y') }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="p-4">
                                <div class="space-y-1">
                                    @if($member->email)
                                        <div class="flex items-center gap-2">
                                            <svg class="h-4 w-4 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                            </svg>
                                            <span>{{ $member->email }}</span>
                                        </div>
                                    @endif
                                    @if($member->telephone)
                                        <div class="flex items-center gap-2">
                                            <svg class="h-4 w-4 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                            </svg>
                                            <span>{{ $member->telephone }}</span>
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td class="p-4">
                                <flux:badge :variant="$member->status === 'active' ? 'success' : ($member->status === 'pending' ? 'warning' : 'danger')">
                                    {{ ucfirst($member->status) }}
                                </flux:badge>
                            </td>
                            <td class="p-4">
                                <div class="flex items-center gap-2">
                                    <flux:button href="{{ route('church-members.show', $member) }}" variant="ghost" size="sm" wire:navigate>
                                        <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        View
                                    </flux:button>
                                    <flux:button href="{{ route('church-members.edit', $member) }}" variant="ghost" size="sm" wire:navigate>
                                        <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                        Edit
                                    </flux:button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="p-4 text-center text-muted-foreground">
                                No members found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t">
            {{ $members->links() }}
        </div>
    </div>
</div>
