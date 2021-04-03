<?php


namespace Database\Seeders;


use App\Models\CashRegister;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProdCashRegisterTableSeeder extends Seeder
{
    public function run()
    {
        CashRegister::query()->truncate();

        DB::table('cash_registers')->insert([
            [
                'title' => 'Main Cash Register',
                'branch_id' => 1,
                'sales_invoice_id' => 2,
                'receiving_invoice_id' => 4,
                'created_by' => 1,
                'created_at' =>now()
            ],
        ]);
    }
}