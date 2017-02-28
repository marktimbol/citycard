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
        foreach( $this->clerks as $clerk )
        {
            $clerk->password = bcrypt('citycard');
            $clerk->save();
        }
        Log::info("Clerk's password are now updated.");
    }
}
