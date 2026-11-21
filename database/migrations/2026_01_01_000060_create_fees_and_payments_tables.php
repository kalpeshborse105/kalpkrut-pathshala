<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fee_structures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institute_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->string('name'); // e.g. JEE Full Year 3-Installment Plan
            $table->decimal('total_amount', 12, 2);
            $table->integer('installment_count')->default(1);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('fee_installments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fee_structure_id')->constrained()->cascadeOnDelete();
            $table->integer('installment_number');
            $table->string('name'); // e.g. "Admission Down-payment", "2nd Quarter", "Final Dues"
            $table->integer('due_days_offset')->default(0); // days after admission
            $table->decimal('percentage', 5, 2)->default(0);
            $table->decimal('amount', 12, 2);
            $table->timestamps();
        });

        Schema::create('student_fee_dues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('admission_id')->constrained()->cascadeOnDelete();
            $table->integer('installment_no')->default(1);
            $table->string('title'); // e.g. Installment 1 / Down Payment
            $table->date('due_date');
            $table->decimal('base_amount', 12, 2);
            $table->decimal('fine_amount', 12, 2)->default(0);
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->decimal('net_amount', 12, 2);
            $table->decimal('paid_amount', 12, 2)->default(0);
            $table->decimal('balance_amount', 12, 2);
            $table->enum('status', ['unpaid', 'partially_paid', 'paid', 'waived'])->default('unpaid');
            $table->timestamps();
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_fee_due_id')->nullable()->constrained('student_fee_dues')->nullOnDelete();
            $table->string('receipt_no')->unique();
            $table->date('payment_date');
            $table->decimal('amount', 12, 2);
            $table->enum('payment_method', ['cash', 'upi', 'card', 'net_banking', 'cheque', 'dd'])->default('cash');
            $table->string('transaction_reference')->nullable();
            $table->string('cheque_bank_name')->nullable();
            $table->string('cheque_no')->nullable();
            $table->date('cheque_date')->nullable();
            $table->foreignId('received_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });

        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->string('voucher_no')->unique();
            $table->string('category'); // Rent, Electricity, Faculty Salary, Stationery, Internet, Marketing, Miscellaneous
            $table->string('title');
            $table->decimal('amount', 12, 2);
            $table->date('expense_date');
            $table->enum('payment_method', ['cash', 'upi', 'bank_transfer', 'cheque'])->default('cash');
            $table->string('paid_to');
            $table->string('reference_no')->nullable();
            $table->string('receipt_file')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expenses');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('student_fee_dues');
        Schema::dropIfExists('fee_installments');
        Schema::dropIfExists('fee_structures');
    }
};

