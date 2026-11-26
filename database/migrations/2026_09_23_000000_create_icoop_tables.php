<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('members')) {
            Schema::create('members', function (Blueprint $t) {
                $t->id();
                $t->foreignId('user_id')->nullable()->unique()->constrained()->nullOnDelete();
                $t->string('member_no')->unique();
                $t->string('full_name');
                $t->string('ic_no')->unique();
                $t->string('email')->nullable();
                $t->string('phone')->nullable();
                $t->text('address')->nullable();
                $t->date('joined_at');
                $t->string('status')->default('active');
                $t->timestamps();
            });
        }
        if (! Schema::hasTable('savings_accounts')) {
            Schema::create('savings_accounts', function (Blueprint $t) {
                $t->id();
                $t->foreignId('member_id')->constrained()->cascadeOnDelete();
                $t->string('account_no')->unique();
                $t->string('account_type')->default('monthly');
                $t->decimal('balance', 14, 2)->default(0);
                $t->string('status')->default('active');
                $t->date('opened_at');
                $t->timestamps();
            });
        }
        if (! Schema::hasTable('savings_transactions')) {
            Schema::create('savings_transactions', function (Blueprint $t) {
                $t->id();
                $t->foreignId('savings_account_id')->constrained()->cascadeOnDelete();
                $t->string('reference_no')->unique();
                $t->string('transaction_type');
                $t->decimal('amount', 14, 2);
                $t->date('transaction_date');
                $t->string('notes', 500)->nullable();
                $t->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
                $t->timestamps();
            });
        }
        if (! Schema::hasTable('financing_products')) {
            Schema::create('financing_products', function (Blueprint $t) {
                $t->id();
                $t->string('name');
                $t->string('code')->unique();
                $t->decimal('profit_rate', 5, 2)->default(0);
                $t->decimal('maximum_amount', 14, 2);
                $t->unsignedSmallInteger('maximum_tenure_months');
                $t->boolean('is_active')->default(true);
                $t->timestamps();
            });
        }
        if (! Schema::hasTable('financing_applications')) {
            Schema::create('financing_applications', function (Blueprint $t) {
                $t->id();
                $t->foreignId('member_id')->constrained();
                $t->foreignId('financing_product_id')->constrained();
                $t->string('application_no')->unique();
                $t->decimal('requested_amount', 14, 2);
                $t->decimal('approved_amount', 14, 2)->nullable();
                $t->unsignedSmallInteger('tenure_months');
                $t->text('purpose')->nullable();
                $t->date('applied_at');
                $t->date('approved_at')->nullable();
                $t->string('status')->default('submitted');
                $t->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
                $t->text('remarks')->nullable();
                $t->timestamps();
            });
        }
        if (! Schema::hasTable('financing_repayments')) {
            Schema::create('financing_repayments', function (Blueprint $t) {
                $t->id();
                $t->foreignId('financing_application_id')->constrained()->cascadeOnDelete();
                $t->string('receipt_no')->unique();
                $t->decimal('amount', 14, 2);
                $t->date('paid_at');
                $t->string('payment_method')->default('online');
                $t->string('notes', 500)->nullable();
                $t->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
                $t->timestamps();
            });
        }
        if (! Schema::hasTable('financial_transactions')) {
            Schema::create('financial_transactions', function (Blueprint $t) {
                $t->id();
                $t->string('transaction_no')->unique();
                $t->date('transaction_date');
                $t->string('transaction_type');
                $t->string('category');
                $t->string('description', 500);
                $t->decimal('amount', 14, 2);
                $t->string('payment_method')->default('online');
                $t->foreignId('member_id')->nullable()->constrained()->nullOnDelete();
                $t->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
                $t->timestamps();
            });
        }
        if (! Schema::hasTable('invoices')) {
            Schema::create('invoices', function (Blueprint $t) {
                $t->id();
                $t->string('invoice_no')->unique();
                $t->foreignId('member_id')->constrained();
                $t->date('issue_date');
                $t->date('due_date')->nullable();
                $t->decimal('subtotal', 14, 2)->default(0);
                $t->decimal('discount', 14, 2)->default(0);
                $t->decimal('total', 14, 2)->default(0);
                $t->string('status')->default('draft');
                $t->text('notes')->nullable();
                $t->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $t->timestamps();
            });
        }
        if (! Schema::hasTable('invoice_items')) {
            Schema::create('invoice_items', function (Blueprint $t) {
                $t->id();
                $t->foreignId('invoice_id')->constrained()->cascadeOnDelete();
                $t->string('description');
                $t->decimal('quantity', 10, 2)->default(1);
                $t->decimal('unit_price', 14, 2);
                $t->decimal('amount', 14, 2);
                $t->timestamps();
            });
        }
        if (! Schema::hasTable('cooperative_settings')) {
            Schema::create('cooperative_settings', function (Blueprint $t) {
                $t->id();
                $t->string('cooperative_name');
                $t->string('registration_no')->nullable();
                $t->string('email')->nullable();
                $t->string('phone')->nullable();
                $t->text('address')->nullable();
                $t->string('logo_path')->nullable();
                $t->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice_items');
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('financial_transactions');
        Schema::dropIfExists('financing_repayments');
        Schema::dropIfExists('financing_applications');
        Schema::dropIfExists('financing_products');
        Schema::dropIfExists('savings_transactions');
        Schema::dropIfExists('savings_accounts');
        Schema::dropIfExists('members');
        Schema::dropIfExists('cooperative_settings');
    }
};
