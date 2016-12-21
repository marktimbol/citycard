Photos
    $table->string('url');
    $table->integer('imageable_id');
    $table->string('imageable_type');

Outlet
	Cover
	Logo

Notifications
	id
	notificationable_id
	notificationable_type

Posts
	id
	merchant_id
	category_id
	type
		is events?
			event_date
			event_time
	title
	slug
	desc
	isExternal
	for_reservation
	for_purchase
	published

Offers

Tickets


Outlet has many Posts->notifications


=======

Threads
	id
	user_id
	clerk_id
	body

Replies
	id
	thread_id
	sender_id
	body


$user->threads()->attach('clerk_id', [
	'body'	=> 'The message'
]);

$user->threads;
$clerk->thread;