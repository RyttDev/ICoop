<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('inventory_memos', function (Blueprint $table): void {
            $table->id();
            $table->string('title')->default('Peringatan Tugas Mingguan');
            $table->string('assigned_staff')->nullable();
            $table->text('body');
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        DB::table('inventory_memos')->insert([
            'id' => 1,
            'title' => 'Peringatan Tugas Mingguan',
            'assigned_staff' => 'Staff bertugas mengikut jadual koperasi',
            'body' => 'Setiap hari Isnin, staff yang bertugas hendaklah menyemak stok fizikal setiap barang dan mengemas kini kuantiti dalam sistem mengikut stok terkini. Maklumkan item yang rendah atau habis kepada admin.',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_memos');
    }
};
