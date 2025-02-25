<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('church_members', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('photo')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->string('home_town');
            $table->text('house_address');
            $table->string('post_office_box')->nullable();
            $table->string('region')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('nationality')->nullable();
            $table->string('telephone')->nullable();
            $table->string('email')->nullable();
            $table->enum('marital_status', ['single', 'married', 'widowed', 'divorced'])->default('single');
            $table->integer('children')->nullable();
            $table->string('occupation')->nullable();
            $table->text('occupation_details')->nullable();
            $table->date('first_visit')->nullable();
            $table->string('right_hand')->nullable();
            $table->string('baptized_by')->nullable();
            $table->enum('baptism', ['yes', 'no'])->nullable();
            $table->date('date_of_baptism')->nullable();
            $table->date('date_converted')->nullable();
            $table->string('mother_name')->nullable();
            $table->string('mother_home_town')->nullable();
            $table->string('mother_occupation')->nullable();
            $table->enum('mother_alive', ['yes', 'no'])->default('yes');
            $table->string('father_name')->nullable();
            $table->string('father_home_town')->nullable();
            $table->string('father_occupation')->nullable();
            $table->enum('father_alive', ['yes', 'no'])->default('yes');
            $table->string('destination_of_transfer')->nullable();
            $table->date('date_of_leaving_the_church')->nullable();
            $table->date('date_of_death')->nullable();
            $table->string('witness_name')->nullable();
            $table->string('witness_contact')->nullable();
            $table->text('witness_address')->nullable();
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_number')->nullable();
            $table->text('emergency_contact_address')->nullable();
            $table->text('emergency_contact_relationship')->nullable();
            $table->text('additional_information')->nullable();
            $table->string('secretary_name')->nullable();
            $table->string('secretary_signature')->nullable();
            $table->string('pastor_name')->nullable();
            $table->string('pastor_signature')->nullable();
            $table->date('application_date')->nullable();
            $table->enum('status', ['active', 'inactive', 'pending'])->default('pending');
            $table->text('spiritual_gifts')->nullable();
            $table->text('ministry_involvement')->nullable();
            $table->enum('preferred_contact_method', ['email', 'phone', 'text'])->nullable();
            $table->date('date_joined')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('church_members');
    }
};
