<?php

use Illuminate\Database\Seeder;

class TickersTableSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        $arr = [];
        for ($i = 1; $i < 30; $i++) {
            array_push($arr, strtoupper(str_random(3)));
        }

        DB::table('tickers')->truncate();
        DB::table('ticker_data')->truncate();
        DB::table('ticker_bots')->truncate();
        DB::table('ticker_recommends')->truncate();
        foreach ($arr as $value) {
            DB::table('tickers')->insert([
                'ticker' => $value,
                'exchange' => str_random(2),
                'outstanding' => rand(10000, 99999),
                'listed' => rand(10000, 99999),
                'treasury' => rand(10000, 99999),
                'foreign_owned' => rand(10000, 99999),
                'equity' => rand(10000, 99999),
            ]);

            $date = new DateTime('2015-01-08');
            for ($i = 1; $i < 60; $i++) {
                $date = date_add($date, new DateInterval('P1D'));
                DB::table('ticker_data')->insert([
                    'ticker' => $value,
                    'date' => $date,
                    'open' => rand(50, 70),
                    'high' => rand(70, 80),
                    'low' => rand(40, 50),
                    'close' => rand(50, 70),
                    'volume' => rand(10000, 99999),
                ]);
            }
        }
    }

}
