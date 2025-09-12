<?php

namespace App\Console\Scheduling;

use Illuminate\Console\Scheduling\Schedule as BaseSchedule;

class Scheduling extends BaseSchedule
{
    public function __construct()
    {
        parent::__construct();
    }
}