<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ChangeClerkPassword implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    public $clerks;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($clerks)
    {
        $this->clerks = $clerks;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->clerks->chunk(300, function($clerks) {        
            foreach( $clerks as $clerk )
            {
                $clerk->password = 'citycard';
                $clerk->save();
            }
        });

        Log::info("Clerk's password are now updated.");
    }
}
