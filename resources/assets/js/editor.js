$(document).ready(function() {
	$('textarea#editor, textarea#editor2, textarea#editor3').froalaEditor({
		height: '300px',
		fontFamily: {
			"Arial,sans-serif": 'Arial',
			"ProximaRegular,sans-serif": 'Proxima Nova',
		},
		fontFamilySelection: true		
	})
});