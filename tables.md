Notifications
	id
	notificationable_id
	notificationable_type

Posts
	type
	title
	slug
	description
	approved
	<!-- image -->
	<!-- start_date -->
	<!-- end_date -->
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