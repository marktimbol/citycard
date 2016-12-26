import Dropzone from 'dropzone';

Dropzone.options.UploadPostPhotos = {
  init: function() {
    this.on("queuecomplete", function(file) {
    	window.location='/dashboard/merchants/'+app.merchant.id;
    });
  }
};

// let UploadPostPhotos = new Dropzone('#UploadPostPhotos', function() {
// 	init: function() {
// 		this.on("queuecomplete", function(file) {
//     		window.location='/dashboard/merchants/'+app.merchant.id;
//     	});
// 	}
// })