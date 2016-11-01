@if (session()->has('flash_notification.message'))
	<script>
		swal({
			title: "City Card",  
			text: "{{ session('flash_notification.message') }}",  
			 type: "{{ session('flash_notification.level') === 'danger' ? 'error' : session('flash_notification.level') }}", 
			 // timer: 2000,
			 showConfirmButton: true,
			 confirmButtonText: 'Okay'
		});
	</script>
@endif

@if (session()->has('flash_message_overlay'))
	<script>
		swal({
			title: "{{ session('flash_message_overlay.title') }}",  
			text: "{{ session('flash_message_overlay.message') }}",  
			type: "{{ session('flash_message_overlay.level') }}", 
			confirmButtonText: 'Okay'
		});
	</script>
@endif