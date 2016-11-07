<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UserCanSendAChatMessageToAClerkTest extends TestCase
{
	use DatabaseMigrations;

    public function test_a_registered_user_can_send_a_chat_message_to_an_outlets_clerk()
    {
    	$user = $this->createUser([
    		'name'	=> 'John'
    	]);
    	$this->actingAs($user, 'user_api');

    	$clerk = $this->createClerk([
    		'first_name'	=> 'Jane'
    	]);

    	$endpoint = sprintf('/api/clerks/%s/messages', $clerk->id);

    	$response = $this->post($endpoint, [
    		'api_token'	=> $user->api_token,
    		'body'	=> 'Hello'
    	]);

    	$this->seeInDatabase('threads', [
    		'user_id'	=> $user->id,
    		'clerk_id'	=> $clerk->id,
    		'body'	=> 'Hello'
    	]);
    }
}
