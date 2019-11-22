<?php

namespace App\Console\Commands;

use App\Interfaces\UserVectorsInterface;
use Illuminate\Console\Command;

class CalculateUsersVector extends Command
{
    protected $userVectors;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'calculate:users-vector {--force}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Расчет вектора перемещения пользователей с помощью ML и сенсоров с устройства';

    /**
     * Create a new command instance.
     *
     * @param UserVectorsInterface $userVectors
     */
    public function __construct( UserVectorsInterface $userVectors)
    {
        $this->userVectors = $userVectors;
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->userVectors->test($this->option('force'));
    }
}
