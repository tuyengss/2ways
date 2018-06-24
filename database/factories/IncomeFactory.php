<?php

use Carbon\Carbon;
use App\Income;
use App\IncomeCategory;

$factory->define(Income::class, function (Faker\Generator $faker) {

    $date = $faker->dateTimeBetween(Carbon::now()->subMonth(11), Carbon::now());

    return [
        "income_category_id" => IncomeCategory::all()->random(1)->first()->id,
        "entry_date" => $date->format('Y-m-d'),
        "amount" => $faker->randomFloat(2, 1, 100),
        "created_at" => $date,
        "updated_at" => $date
    ];
});
