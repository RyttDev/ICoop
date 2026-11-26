<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->string('document_type')->default('invoice')->after('invoice_no');
            $table->string('attention_to')->nullable()->after('member_id');
        });

        Schema::table('cooperative_settings', function (Blueprint $table) {
            $table->string('bank_name')->nullable()->after('phone');
            $table->string('bank_account_no')->nullable()->after('bank_name');
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn(['document_type', 'attention_to']);
        });
        Schema::table('cooperative_settings', function (Blueprint $table) {
            $table->dropColumn(['bank_name', 'bank_account_no']);
        });
    }
};
