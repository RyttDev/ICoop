<?php

namespace Database\Seeders;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PresentationDemoSeeder extends Seeder
{
    public function run(): void
    {
        $year = now()->year;
        $products = [
            'Beras Wangi 5kg', 'Minyak Masak 1kg', 'Gula Putih 1kg', 'Tepung Gandum 1kg', 'Susu Pekat Manis',
            'Biskut Kraker', 'Kopi Segera 3 dalam 1', 'Teh Tarik Segera', 'Mi Segera Kari', 'Sardin Dalam Sos Tomato',
            'Kicap Manis', 'Sos Cili', 'Garam Halus', 'Telur Gred B', 'Roti Sandwic', 'Marjerin 250g',
            'Sabun Basuh Pinggan', 'Serbuk Pencuci', 'Pelembut Pakaian', 'Tisu Gulung', 'Pencuci Lantai',
            'Syampu Rambut', 'Ubat Gigi', 'Berus Gigi', 'Sabun Mandi', 'Air Mineral 1.5L',
            'Minuman Coklat', 'Jus Oren', 'Biskut Coklat', 'Kerepek Kentang', 'Kacang Campur',
            'Sos Tiram', 'Susu UHT', 'Bijirin Sarapan', 'Beras Perang 2kg', 'Madu Asli',
            'Buku Nota A5', 'Pen Dakwat Biru', 'Fail Plastik', 'Kertas A4', 'Mask Muka 3 Ply',
            'Pelitup Hidung', 'Hand Sanitizer', 'Lampin Bayi', 'Tuala Wanita', 'Bateri AA',
            'Mentol LED 9W', 'Beg Kitar Semula', 'Cawan Kertas', 'Bekas Makanan',
        ];
        $categories = ['Makanan Asas', 'Minuman', 'Kudapan', 'Keperluan Rumah', 'Penjagaan Diri', 'Alat Tulis'];

        foreach ($products as $index => $name) {
            $number = $index + 1;
            $quantity = $number % 10 === 0 ? 0 : ($number % 7 === 0 ? 2 + ($number % 3) : 15 + (($number * 7) % 55));
            $reorder = $number % 10 === 0 ? 5 : ($number % 7 === 0 ? 8 : 10);
            $cost = 2.50 + (($number * 1.37) % 18);
            DB::table('stock_items')->updateOrInsert(
                ['sku' => 'ICOOP-'.str_pad((string) $number, 3, '0', STR_PAD_LEFT)],
                [
                    'barcode' => '9559000'.str_pad((string) $number, 5, '0', STR_PAD_LEFT),
                    'name' => $name,
                    'category' => $categories[$index % count($categories)],
                    'supplier_name' => ['Sinar Maju Trading', 'Koperasi Pembekal Utama', 'Murni Niaga Enterprise', 'Rangkaian Sejahtera'][($index % 4)],
                    'description' => 'Stok barangan untuk operasi harian koperasi.',
                    'quantity' => $quantity,
                    'reorder_level' => $reorder,
                    'cost_price' => $cost,
                    'selling_price' => round($cost * 1.28, 2),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        User::where('email', 'demo.laporan@icoop.test')->update(['name' => 'Nur Aina Sofea', 'email' => 'nur.aina@icoop.test', 'updated_at' => now()]);
        $staff = collect([
            ['name' => 'Nur Aina Sofea', 'email' => 'nur.aina@icoop.test'],
            ['name' => 'Muhammad Firdaus', 'email' => 'firdaus@icoop.test'],
            ['name' => 'Siti Hajar Binti Omar', 'email' => 'siti.hajar@icoop.test'],
            ['name' => 'Ahmad Syafiq Rahman', 'email' => 'syafiq.rahman@icoop.test'],
            ['name' => 'Nurul Izzati Hassan', 'email' => 'nurul.izzati@icoop.test'],
        ])->map(fn (array $details) => User::firstOrCreate(
            ['email' => $details['email']],
            ['name' => $details['name'], 'role' => 'staff', 'password' => Hash::make('Koperasi2026!')]
        ))->values();
        DB::table('daily_sales_reports')->where('staff_notes', 'like', 'Rekod demo pembentangan%')->orderBy('sales_date')->get()->each(
            fn (object $report, int $index) => DB::table('daily_sales_reports')->where('id', $report->id)->update([
                'user_id' => $staff[$index % $staff->count()]->id,
                'staff_notes' => 'Serahan jualan harian kaunter koperasi.',
                'admin_notes' => 'Semakan dan pengesahan telah dibuat.',
                'updated_at' => now(),
            ])
        );

        for ($month = 1; $month <= 12; $month++) {
            foreach ([['day' => 5, 'shift' => 'pagi'], ['day' => 18, 'shift' => 'petang']] as $entry) {
                $cash = 280 + ($month * 37) + ($entry['day'] === 18 ? 115 : 0);
                $online = 160 + ($month * 29) + ($entry['day'] === 18 ? 85 : 0);
                $staffMember = $staff[(($month - 1) * 2 + ($entry['day'] === 18 ? 1 : 0)) % $staff->count()];
                DB::table('daily_sales_reports')->updateOrInsert(
                    ['user_id' => $staffMember->id, 'sales_date' => sprintf('%d-%02d-%02d', $year, $month, $entry['day']), 'shift' => $entry['shift']],
                    [
                        'cash_sales' => $cash,
                        'online_sales' => $online,
                        'staff_notes' => 'Serahan jualan harian kaunter koperasi.',
                        'status' => 'approved',
                        'admin_notes' => 'Semakan dan pengesahan telah dibuat.',
                        'reviewed_by' => User::where('role', 'admin')->value('id'),
                        'reviewed_at' => now(),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }
        }

        $date = Carbon::create($year, 1, 1)->startOfDay();
        $yearEnd = Carbon::create($year, 12, 31)->endOfDay();
        $dailyIndex = 0;
        while ($date->lte($yearEnd)) {
            $staffMember = $staff[$dailyIndex % $staff->count()];
            $cash = 210 + (($date->dayOfYear * 17) % 190) + ($date->isWeekend() ? 65 : 0);
            $online = 145 + (($date->dayOfYear * 13) % 150) + ($date->isWeekend() ? 45 : 0);
            DB::table('daily_sales_reports')->updateOrInsert(
                ['user_id' => $staffMember->id, 'sales_date' => $date->toDateString(), 'shift' => 'penuh'],
                [
                    'cash_sales' => $cash,
                    'online_sales' => $online,
                    'staff_notes' => 'Serahan jualan harian kaunter koperasi.',
                    'status' => 'approved',
                    'admin_notes' => 'Semakan dan pengesahan telah dibuat.',
                    'reviewed_by' => User::where('role', 'admin')->value('id'),
                    'reviewed_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
            $date->addDay();
            $dailyIndex++;
        }
    }
}
