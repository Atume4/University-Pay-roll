<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('staff', function (Blueprint $table) {
            $table->id();
            $table->foreignId('department_id')->constrained()->restrictOnDelete();
            $table->string('staff_number')->unique();
            $table->string('name');
            $table->enum('category', ['academic','non_academic']);
            $table->string('grade');
            $table->string('bank_name');
            $table->text('bank_account_number'); // Laravel encrypted cast, not plaintext.
            $table->string('bank_account_name');
            $table->decimal('basic_salary', 15, 2);
            $table->decimal('allowances', 15, 2)->default(0);
            $table->decimal('deductions', 15, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('employee');
            $table->foreignId('department_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('staff_id')->nullable()->constrained('staff')->nullOnDelete();
            $table->boolean('is_active')->default(true);
        });

        Schema::create('payroll_cycles', function (Blueprint $table) {
            $table->id();
            $table->char('payroll_month', 7)->unique(); // YYYY-MM
            $table->enum('status', ['draft', 'submitted', 'approved', 'payment_recorded'])->default('draft');
            $table->foreignId('prepared_by')->constrained('users')->restrictOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });

        Schema::create('payroll_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payroll_cycle_id')->constrained()->cascadeOnDelete();
            $table->foreignId('staff_id')->constrained('staff')->restrictOnDelete();
            $table->text('bank_account_snapshot');
            $table->string('bank_name_snapshot');
            $table->string('account_name_snapshot');
            $table->decimal('basic_salary', 15, 2);
            $table->decimal('allowances', 15, 2);
            $table->decimal('deductions', 15, 2);
            $table->decimal('gross_salary', 15, 2);
            $table->decimal('net_salary', 15, 2);
            $table->timestamps();
            $table->unique(['payroll_cycle_id', 'staff_id']);
        });

        Schema::create('financial_requests', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->foreignId('department_id')->constrained()->restrictOnDelete();
            $table->foreignId('requested_by')->constrained('users')->restrictOnDelete();
            $table->string('title');
            $table->text('purpose');
            $table->decimal('amount', 15, 2);
            $table->enum('status', ['pending_department', 'pending_finance', 'approved', 'rejected', 'payment_recorded'])->default('pending_department');
            $table->timestamps();
        });

        Schema::create('approvals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('financial_request_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->enum('stage', ['department','finance']);
            $table->enum('decision', ['approved','rejected']);
            $table->text('comment')->nullable();
            $table->timestamps();
            $table->unique(['financial_request_id', 'stage']);
        });

        Schema::create('payment_records', function (Blueprint $table) {
            $table->id();
            $table->enum('source_type', ['payroll','request']);
            $table->unsignedBigInteger('source_id');
            $table->decimal('amount', 15, 2);
            $table->enum('method', ['bank','cheque']);
            $table->string('external_reference');
            $table->foreignId('recorded_by')->constrained('users')->restrictOnDelete();
            $table->timestamp('recorded_at');
            $table->timestamps();
            $table->unique(['source_type', 'source_id']);
        });

        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action');
            $table->string('entity_type');
            $table->unsignedBigInteger('entity_id');
            $table->json('metadata')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('payment_records');
        Schema::dropIfExists('approvals');
        Schema::dropIfExists('financial_requests');
        Schema::dropIfExists('payroll_items');
        Schema::dropIfExists('payroll_cycles');
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['department_id']);
            $table->dropForeign(['staff_id']);
            $table->dropColumn(['role', 'department_id', 'staff_id', 'is_active']);
        });
        Schema::dropIfExists('staff');
        Schema::dropIfExists('departments');
    }
};
