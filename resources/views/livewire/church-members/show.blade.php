<?php

use App\Models\ChurchMember;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.app')] class extends Component {
    public ChurchMember $member;

    public function mount(ChurchMember $churchMember): void
    {
        $this->member = $churchMember;
    }
}; ?>

<div>
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-bold">Member Details: {{ $member->name }}</h1>
        <div class="flex gap-4">
            <flux:button href="{{ route('church-members.edit', $member) }}" variant="outline" wire:navigate>
                Edit Member
            </flux:button>
            <flux:button href="{{ route('church-members.index') }}" variant="ghost" wire:navigate>
                Back to List
            </flux:button>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <!-- Personal Information -->
        <div class="rounded-lg border bg-card p-6 shadow-sm">
            <h2 class="mb-4 text-lg font-semibold">Personal Information</h2>
            <div class="space-y-4">
                @if($member->photo)
                    <div class="mb-4">
                        <img src="{{ Storage::url($member->photo) }}" alt="{{ $member->name }}" class="h-32 w-32 rounded-full object-cover">
                    </div>
                @endif

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <div class="text-sm text-gray-500">Full Name</div>
                        <div>{{ $member->name }}</div>
                    </div>
                    <div>
                        <div class="text-sm text-gray-500">Gender</div>
                        <div>{{ ucfirst($member->gender ?? 'Not specified') }}</div>
                    </div>
                    <div>
                        <div class="text-sm text-gray-500">Date of Birth</div>
                        <div>{{ $member->date_of_birth?->format('M d, Y') ?? 'Not specified' }}</div>
                    </div>
                    <div>
                        <div class="text-sm text-gray-500">Nationality</div>
                        <div>{{ $member->nationality ?? 'Not specified' }}</div>
                    </div>
                    <div>
                        <div class="text-sm text-gray-500">Marital Status</div>
                        <div>{{ ucfirst($member->marital_status) }}</div>
                    </div>
                    <div>
                        <div class="text-sm text-gray-500">Children</div>
                        <div>{{ $member->children ?? 'Not specified' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contact Information -->
        <div class="rounded-lg border bg-card p-6 shadow-sm">
            <h2 class="mb-4 text-lg font-semibold">Contact Information</h2>
            <div class="space-y-4">
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <div class="text-sm text-gray-500">Telephone</div>
                        <div>{{ $member->telephone ?? 'Not specified' }}</div>
                    </div>
                    <div>
                        <div class="text-sm text-gray-500">Email</div>
                        <div>{{ $member->email ?? 'Not specified' }}</div>
                    </div>
                    <div>
                        <div class="text-sm text-gray-500">Home Town</div>
                        <div>{{ $member->home_town }}</div>
                    </div>
                    <div>
                        <div class="text-sm text-gray-500">Region</div>
                        <div>{{ $member->region ?? 'Not specified' }}</div>
                    </div>
                    <div class="col-span-2">
                        <div class="text-sm text-gray-500">House Address</div>
                        <div>{{ $member->house_address }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Church Information -->
        <div class="rounded-lg border bg-card p-6 shadow-sm">
            <h2 class="mb-4 text-lg font-semibold">Church Information</h2>
            <div class="space-y-4">
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <div class="text-sm text-gray-500">First Visit</div>
                        <div>{{ $member->first_visit?->format('M d, Y') ?? 'Not specified' }}</div>
                    </div>
                    <div>
                        <div class="text-sm text-gray-500">Date Joined</div>
                        <div>{{ $member->date_joined?->format('M d, Y') ?? 'Not specified' }}</div>
                    </div>
                    <div>
                        <div class="text-sm text-gray-500">Baptism Status</div>
                        <div>{{ ucfirst($member->baptism ?? 'Not specified') }}</div>
                    </div>
                    <div>
                        <div class="text-sm text-gray-500">Baptized By</div>
                        <div>{{ $member->baptized_by ?? 'Not specified' }}</div>
                    </div>
                    <div>
                        <div class="text-sm text-gray-500">Date of Baptism</div>
                        <div>{{ $member->date_of_baptism?->format('M d, Y') ?? 'Not specified' }}</div>
                    </div>
                    <div>
                        <div class="text-sm text-gray-500">Date Converted</div>
                        <div>{{ $member->date_converted?->format('M d, Y') ?? 'Not specified' }}</div>
                    </div>
                    <div>
                        <div class="text-sm text-gray-500">Membership Status</div>
                        <div>
                            <flux:badge :variant="$member->status === 'active' ? 'success' : ($member->status === 'pending' ? 'warning' : 'danger')">
                                {{ ucfirst($member->status) }}
                            </flux:badge>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Additional sections as needed -->
    </div>
</div>
