jQuery(function($){

    var totalUploadSize = 0;
    var totalFiles = 0;
    var maxFiles = 20;
    var uploadErrors = '';

    $("#appNumber").bind("keyup paste", function(){
        setTimeout(jQuery.proxy(function() {
            this.val(this.val().replace(/[^0-9+-]/g, '')); console.log(this.val());
        }, $(this)), 0);
    });
    if(typeof Dropzone !== 'undefined'){
        var previewNode = document.querySelector("#template");
        previewNode.id = "";
        var previewTemplate = previewNode.parentNode.innerHTML;
        previewNode.parentNode.removeChild(previewNode);

        var myDropzone = new Dropzone(".dropzone-container", { // Make the whole body a dropzone
            url: "/dashboard/file-uploads", // Set the url
            thumbnailWidth: 80,
            paramName: 'files',
            thumbnailHeight: 80,
            autoProcessQueue: false,
            acceptedFiles: '.pdf,.png,.jpg,.jpeg,.gif,.doc,.docx,.xls',
            hiddenInputContainer: 'form#dropzonewidget',
            uploadMultiple: true,
            parallelUploads: maxFiles,
            maxFiles: maxFiles,
            previewTemplate: previewTemplate,
            autoQueue: false, // Make sure the files aren't queued until manually added
            previewsContainer: "#previews", // Define the container to display the previews
            clickable: ".fileinput-button", // Define the element that should be used as click trigger to select files.
            successmultiple: function(file, response){
                //these are the only two events that our server returns so if it is anything other than this do an alert
                if(response.status === 'success' || response.status === 'failed'){
                    window.location.reload();
                    //alert(response.status);
                } else {
                    alert("Failed to process request.  Please try again.");
                }
            },
            init: function() {
                var _this = this;
                var timeout;
                this.element.querySelector(".actions .start").addEventListener("click", function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    uploadErrors = '';
                    _this.processQueue();


                    timeout = setTimeout(function(){
                        jQuery('.actions .start')[0].click();
                        document.querySelector(".actions .start").setAttribute("disabled", "disabled");
                        clearTimeout(timeout);
                    }, 100);

                });
            }
        });

        myDropzone.on("error", function(file, response) {
            var errors = response.errors; //this is an object of array
            uploadErrors = errors;
        });

        myDropzone.on("addedfile", function(file) {
            $('#error').html('');
            var addFail = false;
            totalUploadSize = totalUploadSize+file.size;
            totalFiles = totalFiles + 1;

            if(totalFiles >  maxFiles){
                this.removeFile(file);
                addFail = true;
                $('#error').html('<div class="alert alert-danger text-white"><p>Sorry not all your files could be uploaded.  There is a 20 file upload limit.</p></div>');
                //document.querySelector('.btn.btn-success.fileinput-button.dz-clickable').setAttribute("disabled", "disabled");
            }

            if(totalFiles == maxFiles){
                //document.querySelector('.btn.btn-success.fileinput-button.dz-clickable').setAttribute("disabled", "disabled");
            }


            if((file.size/1000) > 2000){
                this.removeFile(file);
                addFail = true;
                $('#error').html('<div class="alert alert-danger text-white text-left"><p>Your selected file <strong>'+file.name+'</strong> cannot be uploaded.  The file size exceeds our <strong>2MB</strong> limit.</p></div>');
            }

            if((totalUploadSize/1000) > 8000){
                if(addFail == false){
                    this.removeFile(file);
                    addFail = true;
                }
                $('#error').html('<div class="alert alert-danger text-white text-left"><p>Sorry, your request cannot be processed in one request.  <strong>Total limit for uploads is 8MB</strong>.  We removed the last file added to make your file sizes smaller.</p></div>')
                //document.querySelector(".actions .start").setAttribute("disabled", "disabled");
            }
            else {
                document.querySelector(".actions .start").removeAttribute("disabled");
                document.querySelector("#dropzone-files").setAttribute('style', 'display:block;');
            }

            if(addFail == true){
                $("#dropzone-files").show();
            }
        });

        myDropzone.on("removedfile", function(file) {
            totalUploadSize = totalUploadSize-file.size;
            totalFiles = totalFiles-1;

            if(totalFiles < maxFiles){
                document.querySelector('.btn.btn-success.fileinput-button.dz-clickable').removeAttribute("disabled");
                $('#error').html('');
            }

            if((totalUploadSize/1000) > 8000){
                $('#error').html('<div class="alert alert-danger text-white text-left"><p>Sorry, your request cannot be processed in one request.  <strong>Total limit for uploads is 8MB</strong>.  Please remove the last file added or make your file sizes smaller.</p></div>')
                document.querySelector(".actions .start").setAttribute("disabled", "disabled");
            }
            else {
                $('#error').html('');
                if(totalFiles == 0){
                    document.querySelector("#dropzone-files").setAttribute('style', 'display:none;');
                    document.querySelector(".actions .start").setAttribute("disabled", "disabled");
                }
                else {
                    document.querySelector(".actions .start").removeAttribute("disabled");
                }
            }

            if(uploadErrors != ''){
                var errorsList = "<ul>";
                for(property in uploadErrors){
                    errorsList += '<li><small>' + uploadErrors[property][0] + '</small></li>';
                    // console.log(uploadErrors[property][0]);
                }
                errorsList += "</ul>";
                $('#error').html('<div class="alert alert-danger text-white">' + errorsList + '</div>');
                // console.log(uploadErrors);
                // console.log(errorsList);
            }
        });

        // Update the total progress bar
        myDropzone.on("totaluploadprogress", function(progress) {

            if($('.file-row.dz-image-preview').length === 0){
                document.querySelector(".actions .start").setAttribute("disabled", "disabled");
            }

            document.querySelector("#total-progress .progress-bar").style.width = progress + "%";
        });

        myDropzone.on("sending", function(file, xhr, formData) {
            // Show the total progress bar when upload starts
            document.querySelector("#total-progress").style.opacity = "1";

            var formValues = $('#dropzonewidget').serializeArray();
            $.each(formValues, function(key, value){
                formData.append(value.name,value.value);
            });
            formData.append('_triggering_element_name', 'sabc_file_manager_block_form');
            formData.append('_triggering_element_value', 'op');
            formData.append('ajax_html_ids[]', 'Upload');
            formData.append("filesize", file.size);
        });

        // Hide the total progress bar when nothing's uploading anymore
        myDropzone.on("queuecomplete", function(progress) {
            document.querySelector("#total-progress").style.opacity = "0";
            document.querySelector(".actions .start").setAttribute("disabled", "disabled");
            myDropzone.removeAllFiles(true);
        });

        // Setup the buttons for all transfers
        // The "add files" button doesn't need to be setup because the config
        // `clickable` has already been specified.
        document.querySelector(".actions .start").onclick = function() {
            myDropzone.enqueueFiles(myDropzone.getFilesWithStatus(Dropzone.ADDED));
        };

        document.querySelector(".actions .cancel").onclick = function() {
            myDropzone.removeAllFiles(true);
            document.querySelector(".actions .start").setAttribute("disabled", "disabled");
            $('#error').html('');
            $("#dropzone-files").hide();
            totalUploadSize = 0;
            totalFiles = 0;

        };

        Dropzone.autoDiscover = false;
    }
});
