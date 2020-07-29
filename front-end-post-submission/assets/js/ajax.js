jQuery(document).ready(function($) {

$("form#create_post").validate({
    // Specify validation rules
    rules: {
      title: "required",
      ptype: "required",
      description: "required",
      excerpt: "required",
      featured_img: "required",
      
    },
    // Specify validation error messages
    messages: {
      firstname: "Please enter title",
      ptype: "Please select type any one",
      description: "Please enter description",
      excerpt: "Please enter excerpt",
      featured_img: "Please choose featured image"
    },
    // Make sure the form is submitted to the destination defined
    // in the "action" attribute of the form when valid
    submitHandler: function(form,e) {
	  e.preventDefault();
	  var formdata = new FormData(form);
	  formdata.append('action', 'wpfeps_create_post');
	  
		// We can also pass the url value separately from ajaxurl for front end AJAX implementations
		$.ajax({
		url:wpfeps_ajax_object.ajax_url,
		data:formdata,
		type:'POST',
		contentType: false,
		processData: false,
		dataType: "json",
		beforeSend: function() {
			$('#status').html('<p>Loading...</p>');
		},
		success: function(response){
		   $('#status').html('<p>'+ response.message +'</p>');
		   if(response.status){
			form.reset();   
		   }
		}
		});
		
		}
	  });

});