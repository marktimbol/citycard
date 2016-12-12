var Dropzone = require('dropzone');

// Dropzone.options.UploadProjectLogo = {
// 	uploadMultiple: false,
// 	maxFilesize: .02, // 50kb
// 	acceptedFiles: 'image/*',
// 	dictDefaultMessage: 'Upload Project Logo. Recommended dimensions: 230 x 85px. Maximum 20kb'
// };

Dropzone.options.UploadPostPhotos = {
  init: function() {
    this.on("queuecomplete", function(file) {
    	window.location='/dashboard/merchants/'+app.merchant.id;
    });
  }
};