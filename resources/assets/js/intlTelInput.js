$(document).ready(function() {
	$("#phone").intlTelInput({
		utilsScript: 'node_modules/intl-tel-input/build/js/utils.js',
		separateDialCode: true,
		initialCountry: 'AE'
	});
});