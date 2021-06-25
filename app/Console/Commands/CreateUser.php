<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CreateUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cms:create_user';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create user';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $roles = cms()->user()
            ->role()
            ->get()
            ->pluck('title','id')
            ->toArray();

        $role = array_search(
            $this->choice('Select Role',$roles),
            $roles
        );

        $first_name = $this->ask('Enter first name');
        $last_name = $this->ask('Enter last name');
        $email = $this->ask('Enter email');
        $password = $this->ask('Enter password');

        try {

            $user = cms()->user()->add(
                $role,
                $first_name,
                $last_name,
                $email,
                $password,
                $password
            );

            $this->info('User id: '. $user->id());

        } catch (\RuntimeException $e) {
            $this->error('Error: ' . $e->getMessage());
        }



    }
}
