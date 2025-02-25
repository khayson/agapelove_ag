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

    public function with(): array
    {
        return [
            'members' => ChurchMember::query()
                ->when($this->search, function ($query) {
                    $query->where(function ($query) {
                        $query->where('name', 'like', '%' . $this->search . '%')
                            ->orWhere('email', 'like', '%' . $this->search . '%')
                            ->orWhere('telephone', 'like', '%' . $this->search . '%');
                    });
                })
                ->when($this->status, function ($query) {
                    $query->where('status', $this->status);
                })
                ->paginate($this->perPage),
        ];
    }
}; ?>

<div>
    <div class="mb-6 flex items-center justify-between gap-4">
        <div class="flex flex-1 items-center gap-4">
            <flux:input
                wire:model.live="search"
                placeholder="Search members..."
                type="search"
                class="max-w-sm"
            />

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
            </flux:select>

            <flux:button href="{{ route('church-members.create') }}" variant="primary" wire:navigate>
                Add Member
            </flux:button>
        </div>
    </div>

    <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
        <div class="relative w-full overflow-auto">
            <table class="w-full caption-bottom text-sm">
                <thead class="[&_tr]:border-b">
                    <tr class="border-b transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted">
                        <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground">Photo</th>
                        <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground">Name</th>
                        <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground">Contact</th>
                        <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground">Status</th>
                        <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground">Actions</th>
                    </tr>
                </thead>
                <tbody class="[&_tr:last-child]:border-0">
                    @foreach($members as $member)
                        <tr class="border-b transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted">
                            <td class="p-4">
                                @if($member->photo_url)
                                    <img src="{{ $member->photo_url }}" alt="{{ $member->name }}" class="h-10 w-10 rounded-full object-cover" />
                                @else
                                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-gray-200">
                                        <span class="text-sm font-medium text-gray-600">
                                            {{ substr($member->name, 0, 2) }}
                                        </span>
                                    </div>
                                @endif
                            </td>
                            <td class="p-4">
                                <div class="font-medium">{{ $member->name }}</div>
                                <div class="text-sm text-gray-500">{{ $member->email }}</div>
                            </td>
                            <td class="p-4">
                                <div>{{ $member->telephone }}</div>
                                <div class="text-sm text-gray-500">{{ $member->home_town }}</div>
                            </td>
                            <td class="p-4">
                                <flux:badge :variant="$member->status === 'active' ? 'success' : ($member->status === 'pending' ? 'warning' : 'danger')">
                                    {{ ucfirst($member->status) }}
                                </flux:badge>
                            </td>
                            <td class="p-4">
                                <div class="flex items-center gap-2">
                                    <flux:button href="{{ route('church-members.edit', $member) }}" variant="ghost" size="sm" wire:navigate>
                                        Edit
                                    </flux:button>
                                    <flux:button href="{{ route('church-members.show', $member) }}" variant="ghost" size="sm" wire:navigate>
                                        View
                                    </flux:button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="p-4">
            {{ $members->links() }}
        </div>
    </div>
</div>
