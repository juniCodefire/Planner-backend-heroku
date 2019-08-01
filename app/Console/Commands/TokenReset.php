<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\User;
use App\Http\Controllers\GeneralTokenDestroyController;

class TokenReset extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'token:reset';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This commands reset all users token for security reason after an hour if you  stay alive field column is 0';
    protected $user;
    protected $token_reset;
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(User $user, GeneralTokenDestroyController $token_reset)
    {
        parent::__construct();

        $this->user = $user;
        $this->token_reset = $token_reset;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $reseted = $this->token_reset->tokenDestroyCron($this->user);

    }
}
