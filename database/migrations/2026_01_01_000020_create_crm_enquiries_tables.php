<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('enquiry_sources', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Walk-in, Google Ads, Facebook, Referral, Website, Newspaper, Banner
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('enquiries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('academic_session_id')->nullable();
            $table->string('enquiry_no')->unique();
            $table->string('student_name');
            $table->string('father_or_guardian_name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone');
            $table->string('alternate_phone')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('previous_school_or_college')->nullable();
            $table->string('previous_percentage_or_grade')->nullable();
            $table->unsignedBigInteger('interested_course_id')->nullable();
            $table->foreignId('enquiry_source_id')->nullable()->constrained('enquiry_sources')->nullOnDelete();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('status', ['new', 'contacted', 'counselling_scheduled', 'demo_attended', 'admission_offered', 'converted', 'dropped', 'closed'])->default('new');
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->date('enquiry_date');
            $table->date('next_follow_up_date')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });

        Schema::create('enquiry_followups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enquiry_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // staff member
            $table->date('followup_date');
            $table->enum('contact_method', ['call', 'whatsapp', 'email', 'in_person', 'sms'])->default('call');
            $table->enum('response_status', ['positive', 'interested', 'neutral', 'call_back_later', 'not_interested', 'wrong_number'])->default('interested');
            $table->text('conversation_summary');
            $table->date('next_followup_date')->nullable();
            $table->timestamps();
        });

        Schema::create('counselling_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enquiry_id')->constrained()->cascadeOnDelete();
            $table->foreignId('counsellor_id')->constrained('users')->cascadeOnDelete();
            $table->dateTime('session_datetime');
            $table->string('mode')->default('in_person'); // in_person, online, phone
            $table->text('career_goals')->nullable();
            $table->text('recommended_courses')->nullable();
            $table->text('counsellor_feedback')->nullable();
            $table->enum('status', ['scheduled', 'completed', 'cancelled', 'no_show'])->default('scheduled');
            $table->timestamps();
        });

        Schema::create('demo_classes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('enquiry_id')->nullable()->constrained()->nullOnDelete();
            $table->string('subject_name');
            $table->string('topic');
            $table->foreignId('faculty_id')->constrained('users')->cascadeOnDelete();
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->string('mode')->default('offline'); // offline, online
            $table->string('meeting_or_room')->nullable();
            $table->enum('status', ['scheduled', 'attended', 'absent', 'feedback_given'])->default('scheduled');
            $table->integer('student_rating')->nullable();
            $table->text('feedback')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('demo_classes');
        Schema::dropIfExists('counselling_sessions');
        Schema::dropIfExists('enquiry_followups');
        Schema::dropIfExists('enquiries');
        Schema::dropIfExists('enquiry_sources');
    }
};

