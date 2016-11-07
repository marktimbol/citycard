<?php

namespace App;

trait CanSendMessage
{
	protected $message;

	public function threads()
	{
	    return $this->belongsToMany(Thread::class, 'threads', 'user_id', 'clerk_id');
	    // return $this->hasMany(Thread::class, 'sender_id');
	}

	public function send($message)
	{
	    $this->message = $message;

	    return $this;
	}

	public function to($receiver)
	{
		$this->threads()->attach($receiver, [
			'body'	=> $this->message
		]);

	    // $thread = $this->threads()->create([
	    //     'receiver_id'   => $receiver->id,
	    //     'body'  => $this->message
	    // ]);

	    // return $thread->messages()->create([
	    //     'sender_id' => $this->id,
	    //     'receiver_id'   => $receiver->id,
	    //     'body'  => $this->message
	    // ]);
	}
}