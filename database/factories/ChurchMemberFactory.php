<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ChurchMemberFactory extends Factory
{
    public function definition(): array
    {
        $spiritualGifts = fake()->randomElements(['Teaching', 'Prophecy', 'Healing', 'Administration', 'Leadership', 'Giving', 'Mercy'], rand(1, 3));
        $ministryInvolvement = fake()->randomElements(['Choir', 'Sunday School', 'Youth Ministry', 'Women Ministry', 'Men Ministry', 'Children Ministry'], rand(1, 3));

        return [
            'name' => fake()->name(),
            'photo' => null,
            'gender' => fake()->randomElement(['male', 'female', 'other']),
            'home_town' => fake()->city(),
            'house_address' => fake()->address(),
            'post_office_box' => fake()->optional()->postcode(),
            'region' => fake()->state(),
            'date_of_birth' => fake()->dateTimeBetween('-80 years', '-18 years'),
            'nationality' => fake()->country(),
            'telephone' => fake()->phoneNumber(),
            'email' => fake()->optional()->safeEmail(),
            'marital_status' => fake()->randomElement(['single', 'married', 'widowed', 'divorced']),
            'children' => fake()->optional()->numberBetween(0, 8),
            'occupation' => fake()->jobTitle(),
            'occupation_details' => fake()->optional()->paragraph(),
            'first_visit' => fake()->dateTimeBetween('-10 years', 'now'),
            'right_hand' => fake()->optional()->randomElement(['right', 'left']),
            'baptized_by' => fake()->optional()->name(),
            'baptism' => fake()->randomElement(['yes', 'no']),
            'date_of_baptism' => fake()->optional()->dateTimeBetween('-10 years', 'now'),
            'date_converted' => fake()->optional()->dateTimeBetween('-20 years', 'now'),
            'mother_name' => fake()->optional()->name('female'),
            'mother_home_town' => fake()->optional()->city(),
            'mother_occupation' => fake()->optional()->jobTitle(),
            'mother_alive' => fake()->randomElement(['yes', 'no']),
            'father_name' => fake()->optional()->name('male'),
            'father_home_town' => fake()->optional()->city(),
            'father_occupation' => fake()->optional()->jobTitle(),
            'father_alive' => fake()->randomElement(['yes', 'no']),
            'destination_of_transfer' => fake()->optional()->city(),
            'date_of_leaving_the_church' => fake()->optional()->dateTimeBetween('-5 years', 'now'),
            'date_of_death' => fake()->optional()->dateTimeBetween('-5 years', 'now'),
            'witness_name' => fake()->optional()->name(),
            'witness_contact' => fake()->optional()->phoneNumber(),
            'witness_address' => fake()->optional()->address(),
            'emergency_contact_name' => fake()->name(),
            'emergency_contact_number' => fake()->phoneNumber(),
            'emergency_contact_address' => fake()->address(),
            'emergency_contact_relationship' => fake()->randomElement(['Spouse', 'Parent', 'Sibling', 'Child', 'Friend']),
            'additional_information' => fake()->optional()->paragraph(),
            'secretary_name' => fake()->optional()->name(),
            'secretary_signature' => null,
            'pastor_name' => fake()->optional()->name(),
            'pastor_signature' => null,
            'application_date' => fake()->dateTimeBetween('-10 years', 'now'),
            'status' => fake()->randomElement(['active', 'inactive', 'pending']),
            'spiritual_gifts' => json_encode($spiritualGifts),
            'ministry_involvement' => json_encode($ministryInvolvement),
            'preferred_contact_method' => fake()->randomElement(['email', 'phone', 'text']),
            'date_joined' => fake()->dateTimeBetween('-10 years', 'now'),
        ];
    }
}
