<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institute_id')->constrained()->cascadeOnDelete();
            $table->string('name'); // e.g. Engineering Prep, Medical Prep, Software Training, Commerce
            $table->string('slug');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institute_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained('course_categories')->nullOnDelete();
            $table->string('name');
            $table->string('code')->unique();
            $table->integer('duration_value');
            $table->enum('duration_unit', ['days', 'weeks', 'months', 'years'])->default('months');
            $table->enum('delivery_mode', ['offline', 'online', 'hybrid'])->default('hybrid');
            $table->decimal('base_fee', 12, 2)->default(0);
            $table->decimal('gst_percentage', 5, 2)->default(18.00);
            $table->decimal('total_fee', 12, 2)->default(0);
            $table->text('description')->nullable();
            $table->text('syllabus_overview')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();
        });

        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('code');
            $table->integer('total_hours')->default(40);
            $table->boolean('is_practical')->default(false);
            $table->text('syllabus')->nullable();
            $table->timestamps();
        });

        Schema::create('classrooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->string('room_number');
            $table->string('room_name')->nullable();
            $table->integer('seating_capacity')->default(30);
            $table->string('floor')->nullable();
            $table->boolean('has_projector')->default(true);
            $table->boolean('has_air_conditioner')->default(true);
            $table->boolean('has_smart_board')->default(false);
            $table->string('status')->default('active');
            $table->timestamps();
        });

        Schema::create('batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->foreignId('academic_session_id')->constrained()->cascadeOnDelete();
            $table->string('name'); // e.g. Batch Alpha 2026 Morning
            $table->string('code')->unique();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->integer('capacity')->default(40);
            $table->enum('delivery_mode', ['offline', 'online', 'hybrid'])->default('offline');
            $table->foreignId('classroom_id')->nullable()->constrained('classrooms')->nullOnDelete();
            $table->foreignId('primary_mentor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('status', ['upcoming', 'active', 'completed', 'cancelled'])->default('upcoming');
            $table->timestamps();
        });

        Schema::create('batch_faculty', function (Blueprint $table) {
            $table->id();
            $table->foreignId('batch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('faculty_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('subject_id')->nullable()->constrained('subjects')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('timetables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('batch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $table->foreignId('faculty_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('classroom_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('day_of_week', ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday']);
            $table->time('start_time');
            $table->time('end_time');
            $table->string('room_or_link')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('timetables');
        Schema::dropIfExists('batch_faculty');
        Schema::dropIfExists('batches');
        Schema::dropIfExists('classrooms');
        Schema::dropIfExists('subjects');
        Schema::dropIfExists('courses');
        Schema::dropIfExists('course_categories');
    }
};

