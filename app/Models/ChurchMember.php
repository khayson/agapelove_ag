<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ChurchMember extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'photo',
        'home_town',
        'house_address',
        'post_office_box',
        'region',
        'date_of_birth',
        'nationality',
        'telephone',
        'email',
        'marital_status',
        'children',
        'occupation',
        'occupation_details',
        'first_visit',
        'right_hand',
        'baptized_by',
        'baptism',
        'date_of_baptism',
        'date_converted',
        'mother_name',
        'mother_home_town',
        'mother_alive',
        'father_name',
        'father_home_town',
        'father_alive',
        'destination_of_transfer',
        'date_of_leaving_the_church',
        'date_of_death',
        'witness_name',
        'witness_contact',
        'witness_address',
        'emergency_contact_name',
        'emergency_contact_number',
        'emergency_contact_address',
        'emergency_contact_relationship',
        'additional_information',
        'secretary_name',
        'pastor_name',
        'pastor_signature',
        'application_date',
        'status',
        'gender',
        'spiritual_gifts',
        'ministry_involvement',
        'preferred_contact_method',
        'date_joined',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        // Add any attributes you want to hide from serialization
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected $casts = [
        'date_of_birth' => 'date',
        'date_of_baptism' => 'date',
        'date_converted' => 'date',
        'date_of_leaving_the_church' => 'date',
        'date_of_death' => 'date',
        'application_date' => 'date',
        'first_visit' => 'date',
        'date_joined' => 'date',
        'spiritual_gifts' => 'array',
        'ministry_involvement' => 'array'
    ];

    protected $appends = ['photo_url'];

    public function getPhotoUrlAttribute()
    {
        return $this->photo
            ? Storage::url($this->photo)
            : null;
    }
}
