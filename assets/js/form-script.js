jQuery(document).ready( function($){

	$('#report-submit').click( function(e){
		e.preventDefault();

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
		var files = inferFormSanitizeFiles();
		if ( ! files.length > 0) {
			inferFormSendError( $('#plupload-upload-ui'), messages.errorUploadedFile );
			return;
		}

		inferFormComplianceFiles( files );
		
		$('#report-submit').fadeTo(250, 1);
		$('#report-submit').attr('disabled', false);

	});


	//Output error and enable button
	function inferFormSendError( element, message ) {

		element.addClass('errorJsValidation');
		$('p.error-message').html( message );

		$('#report-submit').fadeTo(250, 1);
		$('#report-submit').attr('disabled', false);

	}


	//Verify all uploaded files to check if it's csv
	function inferFormSanitizeFiles() {

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
		});

		//Return valid files
		return files;
    
	}


	//Verify valid files for CSV compliance
	function inferFormComplianceFiles( files ) {

		//Check csv file have expected standard
		var data = {
			'action': 'infer_form_compliance_files',
			'files': files
		};
		$.post(messages.ajax_url, data, function(response) {
			files = $.parseJSON( response );
		});

		//Return valid files
		return files;
    
	}

});