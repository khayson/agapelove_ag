<?php

use App\Models\ChurchMember;
use Livewire\Attributes\Layout;
use Livewire\WithFileUploads;
use Livewire\Volt\Component;
use Illuminate\Support\Facades\Storage;

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

        // Format dates before filling
        $dates = [
            'date_of_birth',
            'first_visit',
            'date_of_baptism',
            'date_converted',
            'date_of_leaving_the_church',
            'date_of_death',
            'application_date',
            'date_joined'
        ];

        $data = $churchMember->toArray();
        foreach ($dates as $date) {
            if (isset($data[$date])) {
                $data[$date] = date('Y-m-d', strtotime($data[$date]));
            }
        }

        $this->fill($data);

        // Handle JSON fields properly
        $this->spiritual_gifts = is_string($churchMember->spiritual_gifts)
            ? json_decode($churchMember->spiritual_gifts)
            : [];

        $this->ministry_involvement = is_string($churchMember->ministry_involvement)
            ? json_decode($churchMember->ministry_involvement)
            : [];
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
        if ($this->photo && !is_string($this->photo)) {
            if ($this->member->photo) {
                Storage::disk('public')->delete($this->member->photo);
            }
            $validated['photo'] = $this->photo->store('photos', 'public');
        } else {
            unset($validated['photo']);
        }

        if ($this->secretary_signature && !is_string($this->secretary_signature)) {
            if ($this->member->secretary_signature) {
                Storage::disk('public')->delete($this->member->secretary_signature);
            }
            $validated['secretary_signature'] = $this->secretary_signature->store('signatures', 'public');
        } else {
            unset($validated['secretary_signature']);
        }

        if ($this->pastor_signature && !is_string($this->pastor_signature)) {
            if ($this->member->pastor_signature) {
                Storage::disk('public')->delete($this->member->pastor_signature);
            }
            $validated['pastor_signature'] = $this->pastor_signature->store('signatures', 'public');
        } else {
            unset($validated['pastor_signature']);
        }

        // Handle arrays
        $validated['spiritual_gifts'] = $this->spiritual_gifts ? json_encode($this->spiritual_gifts) : null;
        $validated['ministry_involvement'] = $this->ministry_involvement ? json_encode($this->ministry_involvement) : null;

        // Handle dates
        $dates = [
            'date_of_birth',
            'first_visit',
            'date_of_baptism',
            'date_converted',
            'date_of_leaving_the_church',
            'date_of_death',
            'application_date',
            'date_joined'
        ];

        foreach ($dates as $date) {
            if (isset($validated[$date]) && $validated[$date] !== '') {
                $validated[$date] = date('Y-m-d', strtotime($validated[$date]));
            } else {
                $validated[$date] = null;
            }
        }

        $this->member->update($validated);

        $this->redirect(route('church-members.show', $this->member), navigate: true);
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

    public function downloadMemberDetails()
    {
        $member = $this->member;

        // Prepare the data for download
        $data = [
            'Name' => $member->name,
            'Gender' => $member->gender,
            'Home Town' => $member->home_town,
            'House Address' => $member->house_address,
            'Date of Birth' => $member->date_of_birth ? date('Y-m-d', strtotime($member->date_of_birth)) : '',
            'Email' => $member->email,
            'Telephone' => $member->telephone,
            'Marital Status' => $member->marital_status,
            'Occupation' => $member->occupation,
            'Date Joined' => $member->date_joined ? date('Y-m-d', strtotime($member->date_joined)) : '',
            // Add more fields as necessary
        ];

        // Generate CSV
        $csvFileName = 'member-details-' . $member->id . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$csvFileName}",
        ];

        $callback = function () use ($data) {
            $file = fopen('php://output', 'w');
            fputcsv($file, array_keys($data)); // Header
            fputcsv($file, $data); // Data
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}; ?>

<div>
    <div class="mb-6 flex items-center justify-between space-x-4">
        <h1 class="text-2xl font-bold">Edit Member: {{ $member->name }}</h1>
        <div class="flex space-x-2">
            <flux:button wire:click="delete" variant="danger" wire:confirm="Are you sure you want to delete this member?">
                Delete Member
            </flux:button>
            <flux:button wire:click="downloadMemberDetails" variant="primary">
                Download Details
            </flux:button>
        </div>
    </div>

    <form wire:submit="save" class="space-y-8">
        <!-- Same form fields as create.blade.php -->
        <div class="rounded-lg border bg-card p-6 shadow-sm">
            <h2 class="mb-4 text-lg font-semibold">Personal Information</h2>
            <div class="grid gap-4 sm:grid-cols-2 md:grid-cols-3">
                <!-- Photo Upload -->
                <div class="col-span-full">
                    @if ($photo && !is_string($photo))
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

        <!-- Contact Information -->
        <div class="rounded-lg border bg-card p-6 shadow-sm">
            <h2 class="mb-4 text-lg font-semibold">Contact Information</h2>
            <div class="grid gap-4 sm:grid-cols-2 md:grid-cols-3">
                <flux:input wire:model="telephone" label="Telephone" />
                <flux:input wire:model="email" type="email" label="Email" />
                <flux:select wire:model="preferred_contact_method" label="Preferred Contact Method">
                    <option value="">Select Method</option>
                    <option value="email">Email</option>
                    <option value="phone">Phone</option>
                    <option value="text">Text Message</option>
                </flux:select>
                <flux:input wire:model="house_address" label="House Address" required />
                <flux:input wire:model="post_office_box" label="P.O. Box" />
                <flux:input wire:model="region" label="Region" />
                <flux:input wire:model="home_town" label="Home Town" required />
            </div>
        </div>

        <!-- Church Information -->
        <div class="rounded-lg border bg-card p-6 shadow-sm">
            <h2 class="mb-4 text-lg font-semibold">Church Information</h2>
            <div class="grid gap-4 sm:grid-cols-2 md:grid-cols-3">
                <flux:input wire:model="first_visit" type="date" label="First Visit Date" />
                <flux:input wire:model="date_joined" type="date" label="Date Joined" />
                <flux:input wire:model="right_hand" label="Right Hand of Fellowship By" />
                <flux:select wire:model="baptism" label="Baptized">
                    <option value="">Select Option</option>
                    <option value="yes">Yes</option>
                    <option value="no">No</option>
                </flux:select>
                <flux:input wire:model="baptized_by" label="Baptized By" />
                <flux:input wire:model="date_of_baptism" type="date" label="Date of Baptism" />
                <flux:input wire:model="date_converted" type="date" label="Date Converted" />
                <flux:select wire:model="status" label="Membership Status" required>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                    <option value="pending">Pending</option>
                </flux:select>
            </div>
        </div>

        <!-- Occupation -->
        <div class="rounded-lg border bg-card p-6 shadow-sm">
            <h2 class="mb-4 text-lg font-semibold">Occupation</h2>
            <div class="grid gap-4 sm:grid-cols-2">
                <flux:input wire:model="occupation" label="Occupation" />
                <flux:textarea wire:model="occupation_details" label="Occupation Details" />
            </div>
        </div>

        <!-- Family Information -->
        <div class="rounded-lg border bg-card p-6 shadow-sm">
            <h2 class="mb-4 text-lg font-semibold">Family Information</h2>
            <div class="grid gap-4 sm:grid-cols-2">
                <div class="space-y-4">
                    <h3 class="font-medium">Mother's Information</h3>
                    <flux:input wire:model="mother_name" label="Mother's Name" />
                    <flux:input wire:model="mother_home_town" label="Mother's Home Town" />
                    <flux:input wire:model="mother_occupation" label="Mother's Occupation" />
                    <flux:select wire:model="mother_alive" label="Is Mother Alive?" required>
                        <option value="yes">Yes</option>
                        <option value="no">No</option>
                    </flux:select>
                </div>
                <div class="space-y-4">
                    <h3 class="font-medium">Father's Information</h3>
                    <flux:input wire:model="father_name" label="Father's Name" />
                    <flux:input wire:model="father_home_town" label="Father's Home Town" />
                    <flux:input wire:model="father_occupation" label="Father's Occupation" />
                    <flux:select wire:model="father_alive" label="Is Father Alive?" required>
                        <option value="yes">Yes</option>
                        <option value="no">No</option>
                    </flux:select>
                </div>
            </div>
        </div>

        <!-- Emergency Contact -->
        <div class="rounded-lg border bg-card p-6 shadow-sm">
            <h2 class="mb-4 text-lg font-semibold">Emergency Contact</h2>
            <div class="grid gap-4 sm:grid-cols-2">
                <flux:input wire:model="emergency_contact_name" label="Contact Name" />
                <flux:input wire:model="emergency_contact_number" label="Contact Number" />
                <flux:input wire:model="emergency_contact_relationship" label="Relationship" />
                <flux:textarea wire:model="emergency_contact_address" label="Contact Address" />
            </div>
        </div>

        <!-- Witness Information -->
        <div class="rounded-lg border bg-card p-6 shadow-sm">
            <h2 class="mb-4 text-lg font-semibold">Witness Information</h2>
            <div class="grid gap-4 sm:grid-cols-2">
                <flux:input wire:model="witness_name" label="Witness Name" />
                <flux:input wire:model="witness_contact" label="Witness Contact" />
                <flux:textarea wire:model="witness_address" label="Witness Address" />
            </div>
        </div>

        <!-- Additional Information -->
        <div class="rounded-lg border bg-card p-6 shadow-sm">
            <h2 class="mb-4 text-lg font-semibold">Additional Information</h2>
            <div class="grid gap-4">
                <flux:textarea wire:model="additional_information" label="Additional Information" />
                <flux:input wire:model="destination_of_transfer" label="Destination of Transfer" />
                <flux:input wire:model="date_of_leaving_the_church" type="date" label="Date of Leaving" />
                <flux:input wire:model="date_of_death" type="date" label="Date of Death" />
            </div>
        </div>

        <!-- Signatures -->
        <div class="rounded-lg border bg-card p-6 shadow-sm">
            <h2 class="mb-4 text-lg font-semibold">Signatures</h2>
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <h3 class="mb-2 font-medium">Secretary</h3>
                    <flux:input wire:model="secretary_name" label="Secretary Name" />
                    @if ($secretary_signature && !is_string($secretary_signature))
                    <img src="{{ $secretary_signature->temporaryUrl() }}" class="mb-2 h-32 w-auto">
                    @elseif ($member->secretary_signature)
                    <img src="{{ Storage::url($member->secretary_signature) }}" class="mb-2 h-32 w-auto">
                    @endif
                    <flux:input type="file" wire:model="secretary_signature" label="Secretary Signature" accept="image/*" />
                </div>
                <div>
                    <h3 class="mb-2 font-medium">Pastor</h3>
                    <flux:input wire:model="pastor_name" label="Pastor Name" />
                    @if ($pastor_signature && !is_string($pastor_signature))
                    <img src="{{ $pastor_signature->temporaryUrl() }}" class="mb-2 h-32 w-auto">
                    @elseif ($member->pastor_signature)
                    <img src="{{ Storage::url($member->pastor_signature) }}" class="mb-2 h-32 w-auto">
                    @endif
                    <flux:input type="file" wire:model="pastor_signature" label="Pastor Signature" accept="image/*" />
                </div>
                <flux:input wire:model="application_date" type="date" label="Application Date" />
            </div>
        </div>

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
