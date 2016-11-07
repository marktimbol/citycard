<?php

namespace App;

trait CanReplyToAMessage
{
	protected $body;

	public function reply($body)
	{
		$this->body = $body;

		return $this;
	}

	public function to($thread_id)
	{
		$thread = Thread::findOrFail($thread_id);

		return $thread->replies()->create([
			'from'	=> $this->id,
			'body'	=> $this->body,
		]);
	}
}