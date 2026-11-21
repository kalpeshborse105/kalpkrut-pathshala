<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->foreignId('batch_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('subject_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->string('exam_code')->unique();
            $table->enum('exam_type', ['online', 'offline', 'hybrid'])->default('offline');
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->integer('duration_minutes')->default(60);
            $table->decimal('total_marks', 8, 2)->default(100);
            $table->decimal('passing_marks', 8, 2)->default(40);
            $table->text('instructions')->nullable();
            $table->enum('status', ['draft', 'published', 'in_progress', 'completed', 'results_published'])->default('published');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('exam_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained()->cascadeOnDelete();
            $table->text('question_text');
            $table->enum('question_type', ['mcq', 'true_false', 'short_answer', 'descriptive'])->default('mcq');
            $table->text('option_a')->nullable();
            $table->text('option_b')->nullable();
            $table->text('option_c')->nullable();
            $table->text('option_d')->nullable();
            $table->string('correct_answer'); // e.g. A, B, C, D or True/False
            $table->decimal('marks', 5, 2)->default(1.00);
            $table->decimal('negative_marks', 5, 2)->default(0.00);
            $table->text('explanation')->nullable();
            $table->timestamps();
        });

        Schema::create('student_exam_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->dateTime('started_at')->nullable();
            $table->dateTime('submitted_at')->nullable();
            $table->json('answers')->nullable(); // answers json
            $table->decimal('marks_obtained', 8, 2)->default(0);
            $table->decimal('percentage', 5, 2)->default(0);
            $table->string('grade', 5)->nullable();
            $table->enum('status', ['absent', 'in_progress', 'submitted', 'evaluated'])->default('evaluated');
            $table->enum('result', ['pass', 'fail', 'pending'])->default('pending');
            $table->text('faculty_remarks')->nullable();
            $table->timestamps();
            $table->unique(['exam_id', 'student_id']);
        });

        Schema::create('assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('batch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $table->foreignId('faculty_id')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->dateTime('due_datetime');
            $table->decimal('max_marks', 8, 2)->default(100);
            $table->string('attachment_path')->nullable();
            $table->enum('status', ['active', 'closed', 'archived'])->default('active');
            $table->timestamps();
        });

        Schema::create('assignment_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assignment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->dateTime('submitted_at');
            $table->text('submission_text')->nullable();
            $table->string('file_path')->nullable();
            $table->decimal('marks_obtained', 8, 2)->nullable();
            $table->text('feedback')->nullable();
            $table->enum('status', ['submitted', 'reviewed', 'late', 'resubmit'])->default('submitted');
            $table->timestamps();
            $table->unique(['assignment_id', 'student_id']);
        });

        Schema::create('study_materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subject_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');
            $table->enum('material_type', ['pdf', 'video_link', 'document', 'audio', 'notes'])->default('pdf');
            $table->string('file_path')->nullable();
            $table->text('external_url')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_public_preview')->default(false);
            $table->timestamps();
        });

        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->string('certificate_no')->unique();
            $table->string('title');
            $table->date('issue_date');
            $table->string('grade')->nullable();
            $table->string('verification_code')->unique();
            $table->enum('status', ['issued', 'revoked'])->default('issued');
            $table->foreignId('issued_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('placement_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->string('company_name');
            $table->string('job_title');
            $table->decimal('annual_package_lpa', 8, 2)->nullable();
            $table->date('placement_date');
            $table->string('offer_letter_path')->nullable();
            $table->string('status')->default('placed');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('placement_records');
        Schema::dropIfExists('certificates');
        Schema::dropIfExists('study_materials');
        Schema::dropIfExists('assignment_submissions');
        Schema::dropIfExists('assignments');
        Schema::dropIfExists('student_exam_attempts');
        Schema::dropIfExists('exam_questions');
        Schema::dropIfExists('exams');
    }
};

