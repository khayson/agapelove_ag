<?php

use App\Models\ChurchMember;
use Livewire\Attributes\Layout;
use Livewire\WithFileUploads;
use Livewire\Volt\Component;

new #[Layout('components.layouts.app')] class extends Component {
    use WithFileUploads;

    public ChurchMember $member;
    public $photo;
    public $secretary_signature;
    public $pastor_signature;

    // Member fields (same as create but initialized from member)
    public $name;
    public $gender;
    public $home_town;
    public $house_address;
    public $post_office_box;
    public $region;
    public $date_of_birth;
    public $nationality;
    public $telephone;
    public $email;
    public $marital_status;
    public $children;
    public $occupation;
    public $occupation_details;
    public $first_visit;
    public $right_hand;
    public $baptized_by;
    public $baptism;
    public $date_of_baptism;
    public $date_converted;
    public $mother_name;
    public $mother_home_town;
    public $mother_occupation;
    public $mother_alive;
    public $father_name;
    public $father_home_town;
    public $father_occupation;
    public $father_alive;
    public $destination_of_transfer;
    public $date_of_leaving_the_church;
    public $date_of_death;
    public $witness_name;
    public $witness_contact;
    public $witness_address;
    public $emergency_contact_name;
    public $emergency_contact_number;
    public $emergency_contact_address;
    public $emergency_contact_relationship;
    public $additional_information;
    public $secretary_name;
    public $pastor_name;
    public $application_date;
    public $status;
    public $spiritual_gifts = [];
    public $ministry_involvement = [];
    public $preferred_contact_method;
    public $date_joined;

    public function mount(ChurchMember $churchMember): void
    {
        $this->member = $churchMember;
        $this->fill($churchMember->toArray());
    }

    public function save(): void
    {
        $validated = $this->validate([
            'name' => 'required|string|max:255',
            'photo' => 'nullable|image|max:1024',
            'gender' => 'nullable|in:male,female,other',
            'home_town' => 'required|string|max:255',
            'house_address' => 'required|string',
            'post_office_box' => 'nullable|string|max:255',
            'region' => 'nullable|string|max:255',
            'date_of_birth' => 'nullable|date',
            'nationality' => 'nullable|string|max:255',
            'telephone' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'marital_status' => 'required|in:single,married,widowed,divorced',
            'children' => 'nullable|integer|min:0',
            'occupation' => 'nullable|string|max:255',
            'occupation_details' => 'nullable|string',
            'first_visit' => 'nullable|date',
            'right_hand' => 'nullable|string|max:255',
            'baptized_by' => 'nullable|string|max:255',
            'baptism' => 'nullable|in:yes,no',
            'date_of_baptism' => 'nullable|date',
            'date_converted' => 'nullable|date',
            'mother_name' => 'nullable|string|max:255',
            'mother_home_town' => 'nullable|string|max:255',
            'mother_occupation' => 'nullable|string|max:255',
            'mother_alive' => 'required|in:yes,no',
            'father_name' => 'nullable|string|max:255',
            'father_home_town' => 'nullable|string|max:255',
            'father_occupation' => 'nullable|string|max:255',
            'father_alive' => 'required|in:yes,no',
            'destination_of_transfer' => 'nullable|string|max:255',
            'date_of_leaving_the_church' => 'nullable|date',
            'date_of_death' => 'nullable|date',
            'witness_name' => 'nullable|string|max:255',
            'witness_contact' => 'nullable|string|max:255',
            'witness_address' => 'nullable|string',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_number' => 'nullable|string|max:255',
            'emergency_contact_address' => 'nullable|string',
            'emergency_contact_relationship' => 'nullable|string',
            'additional_information' => 'nullable|string',
            'secretary_name' => 'nullable|string|max:255',
            'secretary_signature' => 'nullable|image|max:1024',
            'pastor_name' => 'nullable|string|max:255',
            'pastor_signature' => 'nullable|image|max:1024',
            'application_date' => 'nullable|date',
            'status' => 'required|in:active,inactive,pending',
            'spiritual_gifts' => 'nullable|array',
            'ministry_involvement' => 'nullable|array',
            'preferred_contact_method' => 'nullable|in:email,phone,text',
            'date_joined' => 'nullable|date',
        ]);

        // Handle file uploads
        if ($this->photo) {
            // Delete old photo if exists
            if ($this->member->photo) {
                Storage::disk('public')->delete($this->member->photo);
            }
            $validated['photo'] = $this->photo->store('member-photos', 'public');
        }
        if ($this->secretary_signature) {
            if ($this->member->secretary_signature) {
                Storage::disk('public')->delete($this->member->secretary_signature);
            }
            $validated['secretary_signature'] = $this->secretary_signature->store('signatures', 'public');
        }
        if ($this->pastor_signature) {
            if ($this->member->pastor_signature) {
                Storage::disk('public')->delete($this->member->pastor_signature);
            }
            $validated['pastor_signature'] = $this->pastor_signature->store('signatures', 'public');
        }

        $this->member->update($validated);

        $this->redirect(route('church-members.index'), navigate: true);
    }

    public function delete(): void
    {
        // Delete associated files
        if ($this->member->photo) {
            Storage::disk('public')->delete($this->member->photo);
        }
        if ($this->member->secretary_signature) {
            Storage::disk('public')->delete($this->member->secretary_signature);
        }
        if ($this->member->pastor_signature) {
            Storage::disk('public')->delete($this->member->pastor_signature);
        }

        $this->member->delete();

        $this->redirect(route('church-members.index'), navigate: true);
    }
}; ?>

<div>
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-bold">Edit Member: {{ $member->name }}</h1>
        <flux:button wire:click="delete" variant="danger" wire:confirm="Are you sure you want to delete this member?">
            Delete Member
        </flux:button>
    </div>

    <form wire:submit="save" class="space-y-8">
        <!-- Same form fields as create.blade.php -->
        <div class="rounded-lg border bg-card p-6 shadow-sm">
            <h2 class="mb-4 text-lg font-semibold">Personal Information</h2>
            <div class="grid gap-4 sm:grid-cols-2 md:grid-cols-3">
                <!-- Photo Upload -->
                <div class="col-span-full">
                    @if ($photo)
                        <img src="{{ $photo->temporaryUrl() }}" class="mb-2 h-32 w-32 rounded-full object-cover">
                    @elseif ($member->photo)
                        <img src="{{ Storage::url($member->photo) }}" class="mb-2 h-32 w-32 rounded-full object-cover">
                    @endif
                    <flux:input type="file" wire:model="photo" label="Photo" accept="image/*" />
                </div>

                <!-- Basic Info -->
                <flux:input wire:model="name" label="Full Name" required />
                <flux:select wire:model="gender" label="Gender">
                    <option value="">Select Gender</option>
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                    <option value="other">Other</option>
                </flux:select>
                <flux:input wire:model="date_of_birth" type="date" label="Date of Birth" />
                <flux:input wire:model="nationality" label="Nationality" />
                <flux:select wire:model="marital_status" label="Marital Status" required>
                    <option value="single">Single</option>
                    <option value="married">Married</option>
                    <option value="widowed">Widowed</option>
                    <option value="divorced">Divorced</option>
                </flux:select>
                <flux:input wire:model="children" type="number" label="Number of Children" min="0" />
            </div>
        </div>

        <!-- Same additional sections as create.blade.php -->

        <div class="flex justify-end gap-4">
            <flux:button href="{{ route('church-members.index') }}" variant="ghost" wire:navigate>
                Cancel
            </flux:button>
            <flux:button type="submit" variant="primary">
                Update Member
            </flux:button>
        </div>
    </form>
</div>
