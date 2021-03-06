<?php

namespace App\Jobs;

use App\Clerk;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ChangeClerkPassword implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    public $action;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($action)
    {
        $this->action = $action;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::info("Changing clerk's password...");

        Clerk::chunk(50, function($clerks) {        
            foreach( $clerks as $clerk )
            {
                $clerk->password = 'citycard';
                $clerk->save();
            }
        });

        Log::info("Clerk's password are now changed.");
    }
}
