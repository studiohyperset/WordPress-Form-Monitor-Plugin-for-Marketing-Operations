jQuery(document).ready( function($){

	$('#report-submit').click( function(e){
		e.preventDefault();

		alert($('input[type="radio"][name="frequency"]:checked').val()); return;

		//Disable button to prevent double
		$(this).fadeTo(250, 0.2);
		$(this).attr('disabled', 'disabled');

		//Reset errors
		$('.errorJsValidation').removeClass('errorJsValidation')
		$('p.error-message').html( '' );

		//Check if name is present
		if ( $('#report-name').val().length == 0 ) {
			inferFormSendError( $('#report-name'), messages.errorNoName )
			return;
		}
		var name = $('#report-name').val();

		//Check if email is present
		if ( $('#report-email').val().length == 0 ) {
			inferFormSendError( $('#report-email'), messages.errorNoEmail );
			return;
		}
		var email = $('#report-email').val(),
			re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;

		//Check if email is valid
    	if ( re.test(email) === false ) {
    		inferFormSendError( $('#report-email'), messages.errorInvalidEmail );
			return;
    	}

		//Check if any files was uploaded
		if ( $('#media-items .media-item').length == 0 ) {
			inferFormSendError( $('#plupload-upload-ui'), messages.errorNoFile );
			return;
		}

		//Check if any files is being uploaded
		if ( $('#media-items .media-item .progress').length > 0 ) {
			inferFormSendError( $('#plupload-upload-ui'), messages.errorUploadingFile );
			return;
		}

		//Check if files are valid
		inferFormSanitizeFiles( function( files ) {

			if ( ! files.length > 0) {
				inferFormSendError( $('#plupload-upload-ui'), messages.errorUploadedFile );
				return;
			}

			inferFormComplianceFiles( files, function( files ){

				if ( ! files.length > 0) {
					inferFormSendError( $('#plupload-upload-ui'), messages.errorUploadedFileInvalid );
					return;
				}
				
				inferFormRegisterTest( files, name, email, $('input[type="radio"][name="frequency"]:checked').val(), function(){

					location.reload();

				});

			});
		});


	});


	//Output error and enable button
	function inferFormSendError( element, message ) {

		element.addClass('errorJsValidation');
		$('p.error-message').html( message );

		$('#report-submit').fadeTo(250, 1);
		$('#report-submit').attr('disabled', false);

	}


	//Verify all uploaded files to check if it's csv
	function inferFormSanitizeFiles( callback ) {

		var files = [],
			regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)");


		//Get file ID from URL
		$('#media-items .media-item').each( function() {

			url = $(this).children('a').attr('href');
			if (url.length == 0) {
				return true;
			}

			name = 'post';
			name = name.replace(/[\[\]]/g, "\\$&");

			results = regex.exec(url);
			if (!results || !results[2]) {
				return true;
			}

			files.push( decodeURIComponent(results[2].replace(/\+/g, " ")) );

		});


		//Check if ID is a file and CSV
		var data = {
			'action': 'infer_form_sanitize_files',
			'files': files
		};
		$.post(messages.ajax_url, data, function(response) {
			files = $.parseJSON( response );
			
			//Return valid files
			callback( files);

		});
	
    
	}


	//Verify valid files for CSV compliance
	function inferFormComplianceFiles( files, callback ) {

		//Check csv file have expected standard
		var newfiles = [],
			data = {
				'action': 'infer_form_compliance_files',
				'files': files
			};

		$.post(messages.ajax_url, data, function(response) {
			newfiles = $.parseJSON( response );

			//Return valid files
			callback( newfiles );

		});

	}


	//Register the test as everything is ok
	function inferFormRegisterTest( files, name, email, frequency, callback ) {

		//Check csv file have expected standard
		var data = {
				'action': 'infer_form_register_test',
				'files': files,
				'name': name,
				'email': email,
				'frequency': frequency
			};

		$.post(messages.ajax_url, data, function(response) {

			if (response == 1) {
				callback( true );
			} else {
				callback( false );
			}

		});

	}

});