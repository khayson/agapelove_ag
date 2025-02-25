<?php

use App\Models\ChurchMember;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Livewire\Volt\Component;
use Illuminate\Support\Str;

new #[Layout('components.layouts.app')] class extends Component {
    use WithPagination;
    use WithFileUploads;

    public $search = '';
    public $status = '';
    public $perPage = 10;
    public $sortField = 'name';
    public $sortDirection = 'asc';
    public $showDeleteModal = false;
    public $memberToDelete = null;
    public $deleteConfirmation = '';
    public $selectedMembers = [];
    public $selectAll = false;
    public $filterGender = '';
    public $filterMaritalStatus = '';
    public $dateRange = '';

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function updatedSelectAll($value)
    {
        $query = ChurchMember::query()
            ->when($this->search, function ($query) {
                $query->where(function ($query) {
                    $query->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%')
                        ->orWhere('telephone', 'like', '%' . $this->search . '%')
                        ->orWhere('home_town', 'like', '%' . $this->search . '%')
                        ->orWhere('occupation', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->status, fn($query) => $query->where('status', $this->status))
            ->when($this->filterGender, fn($query) => $query->where('gender', $this->filterGender))
            ->when($this->filterMaritalStatus, fn($query) => $query->where('marital_status', $this->filterMaritalStatus));

        if ($value) {
            $this->selectedMembers = $query->pluck('id')->map(fn($id) => (string) $id)->toArray();
        } else {
            $this->selectedMembers = [];
        }
    }

    public function exportSelected()
    {
        if (empty($this->selectedMembers)) {
            session()->flash('error', 'Please select members to export.');
            return;
        }

        $members = ChurchMember::whereIn('id', $this->selectedMembers)
            ->orderBy('name', 'asc')
            ->get();

        $csvFileName = 'members-export-' . now()->format('Y-m-d-H-i-s') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$csvFileName}",
        ];

        $columns = [
            'Name', 'Email', 'Telephone', 'Gender', 'Status', 'Occupation',
            'Home Town', 'House Address', 'Date of Birth', 'Marital Status',
            'Date Joined', 'First Visit', 'Baptism Status', 'Date of Baptism',
            'Date Converted', 'Application Date'
        ];

        $callback = function() use ($members, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($members as $member) {
                fputcsv($file, [
                    $member->name,
                    $member->email,
                    $member->telephone,
                    $member->gender,
                    $member->status,
                    $member->occupation,
                    $member->home_town,
                    $member->house_address,
                    $member->date_of_birth ? date('Y-m-d', strtotime($member->date_of_birth)) : '',
                    $member->marital_status,
                    $member->date_joined ? date('Y-m-d', strtotime($member->date_joined)) : '',
                    $member->first_visit ? date('Y-m-d', strtotime($member->first_visit)) : '',
                    $member->baptism,
                    $member->date_of_baptism ? date('Y-m-d', strtotime($member->date_of_baptism)) : '',
                    $member->date_converted ? date('Y-m-d', strtotime($member->date_converted)) : '',
                    $member->application_date ? date('Y-m-d', strtotime($member->application_date)) : ''
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function bulkDelete()
    {
        if (empty($this->selectedMembers)) {
            session()->flash('error', 'Please select members to delete.');
            return;
        }

        $members = ChurchMember::whereIn('id', $this->selectedMembers)->get();

        foreach ($members as $member) {
            // Delete associated files
            if ($member->photo) {
                Storage::disk('public')->delete($member->photo);
            }
            if ($member->secretary_signature) {
                Storage::disk('public')->delete($member->secretary_signature);
            }
            if ($member->pastor_signature) {
                Storage::disk('public')->delete($member->pastor_signature);
            }
        }

        ChurchMember::whereIn('id', $this->selectedMembers)->delete();

        $this->selectedMembers = [];
        $this->selectAll = false;

        session()->flash('success', count($members) . ' members deleted successfully.');
    }

    public function with(): array
    {
        $query = ChurchMember::query()
            ->when($this->search, function ($query) {
                $query->where(function ($query) {
                    $query->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%')
                        ->orWhere('telephone', 'like', '%' . $this->search . '%')
                        ->orWhere('home_town', 'like', '%' . $this->search . '%')
                        ->orWhere('occupation', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->status, fn($query) => $query->where('status', $this->status))
            ->when($this->filterGender, fn($query) => $query->where('gender', $this->filterGender))
            ->when($this->filterMaritalStatus, fn($query) => $query->where('marital_status', $this->filterMaritalStatus))
            ->when($this->dateRange, function($query) {
                // Add date range filter logic
            });

        return [
            'members' => $query->orderBy($this->sortField, $this->sortDirection)->paginate($this->perPage),
            'totalMembers' => ChurchMember::count(),
            'activeMembers' => ChurchMember::where('status', 'active')->count(),
            'inactiveMembers' => ChurchMember::where('status', 'inactive')->count(),
            'pendingMembers' => ChurchMember::where('status', 'pending')->count(),
        ];
    }

    public function confirmDelete($memberId)
    {
        $this->memberToDelete = ChurchMember::find($memberId);
        $this->deleteConfirmation = '';
        $this->showDeleteModal = true;
    }

    public function cancelDelete()
    {
        $this->memberToDelete = null;
        $this->deleteConfirmation = '';
        $this->showDeleteModal = false;
    }

    public function delete()
    {
        if (!$this->memberToDelete) {
            return;
        }

        if ($this->deleteConfirmation !== $this->memberToDelete->name) {
            $this->addError('deleteConfirmation', 'The name you entered does not match.');
            return;
        }

        // Delete associated files
        if ($this->memberToDelete->photo) {
            Storage::disk('public')->delete($this->memberToDelete->photo);
        }
        if ($this->memberToDelete->secretary_signature) {
            Storage::disk('public')->delete($this->memberToDelete->secretary_signature);
        }
        if ($this->memberToDelete->pastor_signature) {
            Storage::disk('public')->delete($this->memberToDelete->pastor_signature);
        }

        $this->memberToDelete->delete();
        $this->memberToDelete = null;
        $this->deleteConfirmation = '';
        $this->showDeleteModal = false;

        session()->flash('success', 'Member deleted successfully.');
    }
}; ?>

<div class="space-y-6 p-4 md:p-6">
    <!-- Stats Overview -->
    <div class="grid grid-cols-2 gap-3 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-xl border bg-card p-4 shadow-sm transition-all hover:shadow-md">
            <div class="flex items-center gap-3">
                <div class="rounded-lg bg-blue-100 p-2 dark:bg-blue-900">
                    <svg class="h-5 w-5 text-blue-600 dark:text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-muted-foreground">Total Members</p>
                    <p class="text-2xl font-bold">{{ $totalMembers }}</p>
                </div>
            </div>
        </div>

        <div class="rounded-xl border bg-card p-4 shadow-sm transition-all hover:shadow-md">
            <div class="flex items-center gap-3">
                <div class="rounded-lg bg-green-100 p-2 dark:bg-green-900">
                    <svg class="h-5 w-5 text-green-600 dark:text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-muted-foreground">Active Members</p>
                    <p class="text-2xl font-bold">{{ $activeMembers }}</p>
                </div>
            </div>
        </div>

        <div class="rounded-xl border bg-card p-4 shadow-sm transition-all hover:shadow-md">
            <div class="flex items-center gap-3">
                <div class="rounded-lg bg-yellow-100 p-2 dark:bg-yellow-900">
                    <svg class="h-5 w-5 text-yellow-600 dark:text-yellow-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-muted-foreground">Pending Members</p>
                    <p class="text-2xl font-bold">{{ $pendingMembers }}</p>
                </div>
            </div>
        </div>

        <div class="rounded-xl border bg-card p-4 shadow-sm transition-all hover:shadow-md">
            <div class="flex items-center gap-3">
                <div class="rounded-lg bg-red-100 p-2 dark:bg-red-900">
                    <svg class="h-5 w-5 text-red-600 dark:text-red-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-muted-foreground">Inactive Members</p>
                    <p class="text-2xl font-bold">{{ $inactiveMembers }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions and Filters -->
    <div class="space-y-4 rounded-xl border bg-card p-4 shadow-sm">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <h1 class="text-xl font-semibold md:text-2xl">Church Members</h1>
            <div class="flex gap-2">
                <flux:button href="{{ route('church-members.create') }}" variant="primary" class="w-full sm:w-auto" wire:navigate>
                    <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span class="hidden sm:inline">Add New Member</span>
                    <span class="sm:hidden">Add</span>
                </flux:button>
            </div>
        </div>

        <!-- Filters Grid -->
        <div class="grid gap-3 md:grid-cols-2 lg:grid-cols-4">
            <!-- Search with debounce -->
            <div class="relative">
                <input
                    wire:model.live.debounce.300ms="search"
                    type="search"
                    placeholder="Search members..."
                    class="w-full rounded-lg border bg-white pl-10 pr-4 py-2 text-sm focus:border-primary focus:ring-1 focus:ring-primary dark:bg-gray-900"
                />
                <svg class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>

            <!-- Status Filter -->
            <flux:select wire:model.live="status" class="w-full">
                <option value="">All Statuses</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
                <option value="pending">Pending</option>
            </flux:select>

            <!-- Gender Filter -->
            <flux:select wire:model.live="filterGender" class="w-full">
                <option value="">All Genders</option>
                <option value="male">Male</option>
                <option value="female">Female</option>
            </flux:select>

            <!-- Marital Status Filter -->
            <flux:select wire:model.live="filterMaritalStatus" class="w-full">
                <option value="">All Marital Statuses</option>
                <option value="single">Single</option>
                <option value="married">Married</option>
                <option value="divorced">Divorced</option>
                <option value="widowed">Widowed</option>
            </flux:select>
        </div>
    </div>

    <!-- Selected Actions Bar -->
    @if (count($selectedMembers) > 0)
    <div class="sticky top-0 z-10 rounded-xl border bg-white p-4 shadow-sm dark:bg-gray-900">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <span class="text-sm font-medium">{{ count($selectedMembers) }} members selected</span>
            <div class="flex gap-2">
                <flux:button wire:click="exportSelected" variant="outline" size="sm" class="flex-1 sm:flex-none">
                    <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    Export
                </flux:button>
                <flux:button wire:click="bulkDelete" variant="danger" size="sm" class="flex-1 sm:flex-none">
                    <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    Delete
                </flux:button>
            </div>
        </div>
    </div>
    @endif

    <!-- Members Table/Grid -->
    <div class="overflow-hidden rounded-xl border bg-card shadow-sm">
        <div class="max-h-96 overflow-y-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead>
                    <tr class="border-b">
                        <th class="w-4 p-4">
                            <flux:checkbox wire:model.live="selectAll" />
                        </th>
                        <th class="p-4 font-medium" wire:click="sortBy('name')" role="button">
                            <div class="flex items-center gap-2">
                                Name
                                @if($sortField === 'name')
                                    <svg class="h-4 w-4 {{ $sortDirection === 'asc' ? '' : 'rotate-180' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                                    </svg>
                                @endif
                            </div>
                        </th>
                        <th class="p-4 font-medium">Contact</th>
                        <th class="p-4 font-medium">Status</th>
                        <th class="p-4 font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($members as $member)
                        <tr class="border-b transition-colors hover:bg-muted/50">
                            <td class="w-4 p-4">
                                <flux:checkbox wire:model.live="selectedMembers" value="{{ $member->id }}" />
                            </td>
                            <td class="p-4">
                                <div class="flex items-center gap-3">
                                    @if($member->photo)
                                        <img src="{{ Storage::url($member->photo) }}" alt="{{ $member->name }}" class="h-10 w-10 rounded-full object-cover">
                                    @else
                                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-primary/10">
                                            <svg class="h-5 w-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                            </svg>
                                        </div>
                                    @endif
                                    <div>
                                        <div class="font-medium">{{ $member->name }}</div>
                                        <div class="text-sm text-muted-foreground">{{ $member->home_town }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="p-4">
                                <div class="space-y-1">
                                    @if($member->email)
                                        <div class="flex items-center gap-2 text-sm">
                                            <svg class="h-4 w-4 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                            </svg>
                                            <span>{{ $member->email }}</span>
                                        </div>
                                    @endif
                                    @if($member->telephone)
                                        <div class="flex items-center gap-2 text-sm">
                                            <svg class="h-4 w-4 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                            </svg>
                                            <span>{{ $member->telephone }}</span>
                                        </div>
                                    @endif
                                    @if($member->occupation)
                                        <div class="flex items-center gap-2 text-sm">
                                            <svg class="h-4 w-4 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                            </svg>
                                            <span>{{ $member->occupation }}</span>
                                        </div>
                                    @endif
                                    @if($member->occupation_details)
                                        <div class="text-sm text-muted-foreground">{{ Str::limit($member->occupation_details, 50) }}</div>
                                    @endif
                                </div>
                            </td>
                            <td class="p-4">
                                @php
                                    $statusColors = [
                                        'active' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
                                        'inactive' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
                                        'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300'
                                    ];
                                @endphp
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $statusColors[$member->status] }}">
                                    {{ ucfirst($member->status) }}
                                </span>
                            </td>
                            <td class="p-4">
                                <div class="flex items-center gap-2">
                                    <flux:button href="{{ route('church-members.show', $member) }}" variant="ghost" size="sm" wire:navigate>
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </flux:button>
                                    <flux:button href="{{ route('church-members.edit', $member) }}" variant="ghost" size="sm" wire:navigate>
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </flux:button>
                                    <flux:button wire:click="confirmDelete({{ $member->id }})" variant="ghost" size="sm">
                                        <svg class="h-4 w-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </flux:button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-4 text-center text-muted-foreground">
                                No members found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t p-4">
            <div class="flex items-center justify-between gap-4">
                <div class="flex items-center gap-2">
                    <span class="text-sm text-muted-foreground">Show</span>
                    <flux:select wire:model.live="perPage" class="w-20">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </flux:select>
                    <span class="text-sm text-muted-foreground">entries</span>
                </div>
                {{ $members->links() }}
            </div>
        </div>
    </div>

    <!-- Delete Modal -->
    @include('livewire.church-members.partials.delete-modal')

    <!-- Success Toast -->
    @if (session()->has('success'))
        <div
            x-data="{ show: true }"
            x-show="show"
            x-transition
            x-init="setTimeout(() => show = false, 3000)"
            class="fixed bottom-4 right-4 z-50 flex items-center rounded-lg bg-green-500 px-4 py-2 text-white shadow-lg"
        >
            <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            {{ session('success') }}
        </div>
    @endif
</div>
