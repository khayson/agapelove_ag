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

        <!-- Occupation Information -->
        <div class="rounded-lg border bg-card p-6 shadow-sm">
            <h2 class="mb-4 text-lg font-semibold">Occupation Information</h2>
            <div class="space-y-4">
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <div class="text-sm text-gray-500">Occupation</div>
                        <div>{{ $member->occupation ?? 'Not specified' }}</div>
                    </div>
                    <div>
                        <div class="text-sm text-gray-500">Occupation Details</div>
                        <div>{{ $member->occupation_details ?? 'Not specified' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Family Information -->
        <div class="rounded-lg border bg-card p-6 shadow-sm">
            <h2 class="mb-4 text-lg font-semibold">Family Information</h2>
            <div class="space-y-4">
                <div>
                    <h3 class="mb-2 font-medium">Mother's Information</h3>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <div class="text-sm text-gray-500">Name</div>
                            <div>{{ $member->mother_name ?? 'Not specified' }}</div>
                        </div>
                        <div>
                            <div class="text-sm text-gray-500">Home Town</div>
                            <div>{{ $member->mother_home_town ?? 'Not specified' }}</div>
                        </div>
                        <div>
                            <div class="text-sm text-gray-500">Occupation</div>
                            <div>{{ $member->mother_occupation ?? 'Not specified' }}</div>
                        </div>
                        <div>
                            <div class="text-sm text-gray-500">Status</div>
                            <div>{{ ucfirst($member->mother_alive) }}</div>
                        </div>
                    </div>
                </div>
                <div>
                    <h3 class="mb-2 font-medium">Father's Information</h3>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <div class="text-sm text-gray-500">Name</div>
                            <div>{{ $member->father_name ?? 'Not specified' }}</div>
                        </div>
                        <div>
                            <div class="text-sm text-gray-500">Home Town</div>
                            <div>{{ $member->father_home_town ?? 'Not specified' }}</div>
                        </div>
                        <div>
                            <div class="text-sm text-gray-500">Occupation</div>
                            <div>{{ $member->father_occupation ?? 'Not specified' }}</div>
                        </div>
                        <div>
                            <div class="text-sm text-gray-500">Status</div>
                            <div>{{ ucfirst($member->father_alive) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Emergency Contact -->
        <div class="rounded-lg border bg-card p-6 shadow-sm">
            <h2 class="mb-4 text-lg font-semibold">Emergency Contact</h2>
            <div class="space-y-4">
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <div class="text-sm text-gray-500">Name</div>
                        <div>{{ $member->emergency_contact_name ?? 'Not specified' }}</div>
                    </div>
                    <div>
                        <div class="text-sm text-gray-500">Contact Number</div>
                        <div>{{ $member->emergency_contact_number ?? 'Not specified' }}</div>
                    </div>
                    <div>
                        <div class="text-sm text-gray-500">Relationship</div>
                        <div>{{ $member->emergency_contact_relationship ?? 'Not specified' }}</div>
                    </div>
                    <div>
                        <div class="text-sm text-gray-500">Address</div>
                        <div>{{ $member->emergency_contact_address ?? 'Not specified' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Witness Information -->
        <div class="rounded-lg border bg-card p-6 shadow-sm">
            <h2 class="mb-4 text-lg font-semibold">Witness Information</h2>
            <div class="space-y-4">
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <div class="text-sm text-gray-500">Name</div>
                        <div>{{ $member->witness_name ?? 'Not specified' }}</div>
                    </div>
                    <div>
                        <div class="text-sm text-gray-500">Contact</div>
                        <div>{{ $member->witness_contact ?? 'Not specified' }}</div>
                    </div>
                    <div class="col-span-2">
                        <div class="text-sm text-gray-500">Address</div>
                        <div>{{ $member->witness_address ?? 'Not specified' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Additional Information -->
        <div class="rounded-lg border bg-card p-6 shadow-sm">
            <h2 class="mb-4 text-lg font-semibold">Additional Information</h2>
            <div class="space-y-4">
                <div>
                    <div class="text-sm text-gray-500">Additional Notes</div>
                    <div>{{ $member->additional_information ?? 'Not specified' }}</div>
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <div class="text-sm text-gray-500">Destination of Transfer</div>
                        <div>{{ $member->destination_of_transfer ?? 'Not specified' }}</div>
                    </div>
                    <div>
                        <div class="text-sm text-gray-500">Date of Leaving</div>
                        <div>{{ $member->date_of_leaving_the_church?->format('M d, Y') ?? 'Not specified' }}</div>
                    </div>
                    <div>
                        <div class="text-sm text-gray-500">Date of Death</div>
                        <div>{{ $member->date_of_death?->format('M d, Y') ?? 'Not specified' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Signatures -->
        <div class="rounded-lg border bg-card p-6 shadow-sm">
            <h2 class="mb-4 text-lg font-semibold">Signatures</h2>
            <div class="space-y-4">
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <h3 class="mb-2 font-medium">Secretary</h3>
                        <div>
                            <div class="text-sm text-gray-500">Name</div>
                            <div>{{ $member->secretary_name ?? 'Not specified' }}</div>
                        </div>
                        @if($member->secretary_signature)
                            <div class="mt-2">
                                <div class="text-sm text-gray-500">Signature</div>
                                <img src="{{ Storage::url($member->secretary_signature) }}" alt="Secretary Signature" class="mt-1 h-16">
                            </div>
                        @endif
                    </div>
                    <div>
                        <h3 class="mb-2 font-medium">Pastor</h3>
                        <div>
                            <div class="text-sm text-gray-500">Name</div>
                            <div>{{ $member->pastor_name ?? 'Not specified' }}</div>
                        </div>
                        @if($member->pastor_signature)
                            <div class="mt-2">
                                <div class="text-sm text-gray-500">Signature</div>
                                <img src="{{ Storage::url($member->pastor_signature) }}" alt="Pastor Signature" class="mt-1 h-16">
                            </div>
                        @endif
                    </div>
                    <div>
                        <div class="text-sm text-gray-500">Application Date</div>
                        <div>{{ $member->application_date?->format('M d, Y') ?? 'Not specified' }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
