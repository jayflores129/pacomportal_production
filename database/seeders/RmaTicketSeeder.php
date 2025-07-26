<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RmaTicketSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('rma_tickets')->get()->each(function ($ticket) {
            $date = \DateTime::createFromFormat('d/m/Y', $ticket->requested_date);

            if ($date) {
                DB::table('rma_tickets')->where('id', $ticket->id)
                ->update([
                    'requested_date' => $date->format('Y-m-d')
                ]);
            } 
        });
    }
}
