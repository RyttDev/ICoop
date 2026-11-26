<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('stock_items', function (Blueprint $table): void {
            $table->string('barcode')->nullable()->unique()->after('sku');
            $table->string('supplier_name')->nullable()->after('category');
            $table->string('image_path')->nullable()->after('description');
        });
        Schema::table('invoices', function (Blueprint $table): void {
            $table->decimal('tax_rate', 5, 2)->default(0)->after('discount');
            $table->decimal('tax_amount', 14, 2)->default(0)->after('tax_rate');
            $table->decimal('shipping_fee', 14, 2)->default(0)->after('tax_amount');
            $table->date('quotation_expires_at')->nullable()->after('due_date');
            $table->timestamp('paid_at')->nullable()->after('status');
        });
        Schema::table('members', function (Blueprint $table): void {
            $table->string('document_path')->nullable()->after('address');
        });
        Schema::create('activity_logs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action');
            $table->string('subject_type')->nullable();
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->text('details')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
        Schema::table('members', fn (Blueprint $table) => $table->dropColumn('document_path'));
        Schema::table('invoices', fn (Blueprint $table) => $table->dropColumn(['tax_rate','tax_amount','shipping_fee','quotation_expires_at','paid_at']));
        Schema::table('stock_items', fn (Blueprint $table) => $table->dropColumn(['barcode','supplier_name','image_path']));
    }
};
