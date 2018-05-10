/*
 * jQuery File Upload Plugin JS Example
 * https://github.com/blueimp/jQuery-File-Upload
 *
 * Copyright 2010, Sebastian Tschan
 * https://blueimp.net
 *
 * Licensed under the MIT license:
 * https://opensource.org/licenses/MIT
 */

/* global $, window */

var jfu_maxFiles = 1;
jfu_fileCount = 0;
var jfu_widgetCount = 0;
var existFileName = '';
var handlerUrl = '/vendor/jquery-file-upload/server/php/';
var isResumingSameFile = false;

var jfuInit = function (j, fileId) {
    'use strict';
    if (fileId != null)
        var fileIdStr = '_' + fileId.toString();
    else
        var fileIdStr = '';

    console.log(' Initialize the jQuery File Upload widget... j='+j.attr('class'));
    j.fileupload({
        // Uncomment the following to send cross-domain cookies:
        //xhrFields: {withCredentials: true},
        url: handlerUrl
    });
    j.bind("fileuploaddone", function (e, data) {
        console.log('fileuploaddone for ' + fileId);
        /* get the upload file name and temp upload path from the response. Basemodel will
        use the paths for copying the file over to the destination dir */
        //console.log("fileuploaddone event fired , data=", data);
        var mName = data.result.files[0].name;
        var mPath = data.result.files[0].url;
        // get the jfu generated temp path from the url
        // e.g. https://.../vendor/jquery-file-upload/server/php/jfu_upload_url/5i5m1svqusapag5ma0tb1tsm15/foobar.png
        mPath = mPath.replace(/^http.*\/jfu_upload_url\//, "");  // smctodo: move this to a config
        console.log(mName, mPath);
        $('#jfu_media_file_name'+fileIdStr).val(mName);
        $('#jfu_media_file_path'+fileIdStr).val(mPath);
        $('#jfu_media_file_partial'+fileIdStr).val('');
        var mStatus = '<span class="status_file_name">' + mName + '</span> <span class="check">&#10004;</span>'; 
        setFileStatus(fileId, mName, 100, false);
        // smctodo: disable select file button, enable Save button
        j.find('.jfu-file-select').prop('disabled', true);
        j.find('.fileinput-button').addClass('disabled');
        //$('#btn-save').prop('disabled', false);
        //$('#btn-save').removeClass('disabled');
        jfu_fileCount = 1;
        existFileName = mName;
    });
    j.bind('fileuploaddestroyed', function (e, data) {
        console.log('in fileuploaddestroyed');
        // after file is deleted, clear file name and temp path
        /* smc 04/07: emptying these fields will cause a problem 
            after deleting a media form, because the fields will be
            blank after re-indexing.  Remove below later after more testing.
        $('#jfu_media_file_name'+fileIdStr).val('');
        $('#jfu_media_file_path'+fileIdStr).val('');        
        $('#jfu_media_file_partial'+fileIdStr).val('');
        setFileStatus(fileId, '');
        */

        // smctodo: enable select file button, disable Save button
        j.find('.jfu-file-select').prop('disabled', false);
        j.find('.fileinput-button').removeClass('disabled');
        //$('#btn-save').prop('disabled', true);
        //$('#btn-save').addClass('disabled');
        jfu_fileCount = 0;
        existFileName = '';
    });

    // Enable iframe cross-domain access via redirect option:
    j.fileupload(
        'option',
        'redirect',
        window.location.href.replace(
            /\/[^\/]*$/,
            '/cors/result.html?%s'
        )
    );

    //console.log('fileIdStr '+fileIdStr);
    var fContainer = $('#presentation'+fileIdStr).closest('tbody[class=files]');
//    var fContainer = $('tbody[class=files'+fileIdStr+']');
    //console.log('fContainer '+fContainer.attr('class'));
    
    j.fileupload('option', {
        url: handlerUrl,
        acceptFileTypes: /(\.|\/)(gif|jpe?g|png|zip|csv)$/i,
        //maxFileSize: 9,
        maxChunkSize: 1000000, // 1 MB
        autoUpload: true,
        disableImageResize:true,
        previewThumbnail:false,
        maxNumberOfFiles:5,
        uploadTemplateId:'template-upload',
        filesContainer:fContainer,
        // resuming file uploads: https://github.com/blueimp/jQuery-File-Upload/wiki/Chunked-file-uploads
        add: function (e, data) {
            console.log('smc: in add function for ' +this.id  + ' -- jfu_fileCount:'+ jfu_fileCount);
            console.log('selected file : ' + data.files[0].name);
            var partialFile = $('#jfu_media_file_partial'+fileIdStr).val();
            console.log('checking partialFile : '+ partialFile);
            if (partialFile != '') {
                // there is a partial file associated with this button.  
                //  we need to make sure the user is selecting the same file associated with the button
                if (data.files[0].name !== partialFile) {
                    // user tries to upload a different file
                    alert('Please delete existing file if you would like to upload another file.');
                    return false;
                } else if (data.files[0].size === data.uploadedBytes) {
                    // user tries to upload the same file, but the file is completely uploaded already
                    alert('The file is already uploaded.  Please fill out the rest of the information and click Save');
                    return false;
                } else {
                    // user is resuming the upload of the same file
                    isResumingSameFile = true;
                }
            }
            // no partial file associated. This means the user should be uploading a new file  
            var that = this;
            $.getJSON(handlerUrl, {file: data.files[0].name}, function (result) {
                //console.log('result ',result);
                var file = result.file;
                if (file != null) {
                    if (!isResumingSameFile) {
                        // selected file is found on the server. But the partial file is not recorded with the button
                        // this means the file is probably uploaded by another button.
                        alert('The file ' + file.name + ' is already uploaded (or being uploaded).');
                        return false;
                    }
                } else {
                    // selected file is not found on the server.  Should be safe to upload
                    // remove this else block later
                }

                data.uploadedBytes = file && file.size;
                //data.uploadedBytes = file && Number(file.size);
                //console.log('uploadedbytes: ',data.uploadedBytes);
                console.log('file: '+ data.files[0].name + ' size: '+ data.files[0].size);

                // save original file size associated with the file name
                localStorage.setItem(data.files[0].name, data.files[0].size);
                console.log('file ' + data.files[0].name + ' ,  stored in localStorage: '+ localStorage.getItem(data.files[0].name));

                // before upload or resume upload, check if there is an existing file (partial or completely uploaded) 
                /* moved - delete later
                if (jfu_fileCount >= jfu_maxFiles) {
                    if (data.files[0].name !== existFileName) {
                        // user tries to upload a different file
                        alert('Please delete existing file if you would like to upload another file.' + data.files[0].name);
                        return false;
                    } else if (data.files[0].size === data.uploadedBytes) {
                        // user tries to upload the same file, but the file is completely uploaded already
                        alert('The file is already uploaded.  Please fill out the rest of the information and click Save');
                        return false;
                    }
                } */

                // disable upload button before upload or resume upload
                j.find('.jfu-file-select').prop('disabled', true);
                j.find('.fileinput-button').addClass('disabled');
                // clear upload from server select dropdown
                // smctodo: which one?
                //$('select[name="mediaServerPath"]').val('');
                var serverSelectId = 'select[id="media'+fileIdStr+'_mediaServerPath"]';
                if ($(serverSelectId).length != 0)
                    $(serverSelectId).val('');                
                $('#jfu_media_file_partial'+fileIdStr).val(data.files[0].name);    
                setFileStatus(fileId, '');

                // add cancel button here
                setFileStatus(fileId, data.files[0].name, 999, true);

                // smctodo
                // delete the old download template if it exists
                /*
                if ($('.template-download').length === 1) {
                    $('.template-download').remove();
                } */
                // upload or resume upload
                $.blueimp.fileupload.prototype
                    .options.add.call(that, e, data);
            });
                
                
        },
        // smc testing auto resume
        maxRetries: 100,
        retryTimeout: 500,
        fail: function (e, data) {
            console.log('smc: in auto resume fail function');
            // jQuery Widget Factory uses "namespace-widgetname" since version 1.10.0:
            var fu = $(this).data('blueimp-fileupload') || $(this).data('fileupload'),
                retries = data.context.data('retries') || 0,
                retry = function () {
                    console.log('smc: in auto resume retry function');
                    $.getJSON(handlerUrl, {file: data.files[0].name})
                        .done(function (result) {
                            var file = result.file;
                            data.uploadedBytes = file && file.size;
                            // clear the previous data:
                            data.data = null;
                            data.submit();
                        })
                        .fail(function () {
                            fu._trigger('fail', e, data);
                        });
                };
            if (data.errorThrown !== 'abort' &&
                    data.uploadedBytes < data.files[0].size &&
                    retries < fu.options.maxRetries) {
                retries += 1;
                console.log('smc: retries = ' + retries);
                data.context.data('retries', retries);
                window.setTimeout(retry, retries * fu.options.retryTimeout);
                return;
            } else {
                // user aborts upload by clicking Cancel button.  Delete the partial upload.
                // "this" is a .jr-group div
                /*
                var a = $(this).find('.fileinput-button').attr('id').split('_');
                var idx = a[1];
                
                
                
                
                setTimeout(function(){ 
                    var b = '#jfu_delete_btn_'+idx;
                    console.log('user aborted upload, about to click on delete button ' + b );
                    $(b).trigger('click');
                }, 2500);
                */
            }
            data.context.removeData('retries');
            $.blueimp.fileupload.prototype
                .options.fail.call(this, e, data);

            // BEGIN: Load existing files after canceling an upload
            j.addClass('fileupload-processing');
            $.ajax({
                // Uncomment the following to send cross-domain cookies:
                //xhrFields: {withCredentials: true},
                url: j.fileupload('option', 'url'),
                dataType: 'json',
                context: j[0]
            }).always(function () {
                $(this).removeClass('fileupload-processing');
            }).done(function (result) {
                jfu_fileCount = result.files.length;
                //console.log('load existing files, return files count:',jfu_fileCount);
                if (jfu_fileCount === 1) {
                    existFileName = result.files[0].name;
                    //console.log(existFileName);
                }
                // disable upload button if there is existing file
                //$('.jfu-file-select').prop('disabled', true);        
                $(this).fileupload('option', 'done')
                    .call(this, $.Event('done'), {result: result});
            });
            // END: Load existing files after canceling an upload                
            
            
            // enable upload button after canceling 
            j.find('.jfu-file-select').prop('disabled', false);
            j.find('.fileinput-button').removeClass('disabled');
            
        }            
    });

    // Load existing files when the widget is initialized:
    j.addClass('fileupload-processing');
    $.ajax({
        // Uncomment the following to send cross-domain cookies:
        //xhrFields: {withCredentials: true},
        url: j.fileupload('option', 'url'),
        dataType: 'json',
        context: j[0]
    }).always(function () {
        $(this).removeClass('fileupload-processing');
    }).done(function (result) {
        jfu_fileCount = result.files.length;
        console.log('...load existing files, return files count:',jfu_fileCount);
        /*  smctodo: how to handle existing file differently for single upload?
        if (jfu_fileCount === 1) {
            existFileName = result.files[0].name;
            console.log(existFileName);
        }*/
        
        // on page load / reload, check existing files on server
        if (jfu_widgetCount == 1) {
            if (jfu_fileCount > 0) {
                console.log('Found existing files on the server');

                populateForms(result);
            } else {
                // if there is no existing file, clear all the original file sizes (since no need to compare)
                console.log('no existing files... clearing localStorage');
                localStorage.clear();

            }

            
        }
        
        
        // disable upload button if there is existing file
        //$('.jfu-file-select').prop('disabled', true);        
        $(this).fileupload('option', 'done')
            .call(this, $.Event('done'), {result: result});
    });

};

var setFileStatus = function(fileId, mName, percent, inProgress) {
    if (inProgress == null)
        inProgress = false;
    var btnDelete = " <a class='jfu_media_file_delete_button' id='jfu_media_file_delete_button_"+fileId+"' href='javascript:void(0)' onClick='jfu_customDelete(\"" + mName + "\", " + fileId + ")'>delete</a> ";
    var btnCancel = " <a class='jfu_media_file_cancel_button' id='jfu_media_file_cancel_button_"+fileId+"' href='javascript:void(0)' onClick='jfu_customCancel(\"" + mName + "\", " + fileId + ")'>cancel upload</a> ";
    if (mName == '') {
        // clear the status
        mDisplay = '<span class="status_file_name"></span>&nbsp;';
    } else {
        if (percent == 100) {
            mDisplay = '<span class="status_file_name">' + mName + '</span> <span class="check">&#10004;</span>' + btnDelete;
        } else {
            if (inProgress) {
                mDisplay = '<span class="status_file_name">' + mName + '</span> ' + btnCancel; 
            } else {
                mDisplay = '<span class="status_file_name">' + mName + '</span> : <span class="warning">' + percent + 
                    '% uploaded <br>Please select the same file to resume upload, or ' + btnDelete + ' the file </span>';
            }
        }
    }
    $('#jfu_media_file_partial_'+fileId).next('.jfu_file_status').html(mDisplay);
}
    
var populateForms = function(result) {
    console.log('in popuplateforms, jfu_filecount = ' +jfu_fileCount);
    // first form is already added.  Populate first form, then loop and populate the rest

    /* this is taken from main.js.  Might need to share the code later
    get the upload file name and temp upload path from the response. Basemodel will
    use the paths for copying the file over to the destination dir */
    var i = fileId = 0;
    var fileIdStr = '_' + i.toString();
    var mName = result.files[i].name;
    var mPath = result.files[i].url;
    var mSize = result.files[i].size;
    var inProgress = false;
    var percent;
    // get the jfu generated temp path from the url
    // e.g. https://.../vendor/jquery-file-upload/server/php/jfu_upload_url/5i5m1svqusapag5ma0tb1tsm15/foobar.png
    mPath = mPath.replace(/^http.*\/jfu_upload_url\//, "");  // smctodo: move this to a config
    console.log(mName, mPath);
    
    var originalSize = localStorage.getItem(mName);
    if (originalSize != null) {
        
            if (originalSize == mSize) {
                // completely uploaded
                $('#jfu_media_file_name'+fileIdStr).val(mName);
                $('#jfu_media_file_path'+fileIdStr).val(mPath);
                $('#jfu_media_file_partial'+fileIdStr).val('');
                percent = 100;
                $('#jfu-file-select'+fileIdStr).prop('disabled', true);
                $('#jfu-file-select'+fileIdStr).closest('.fileinput-button').addClass('disabled');
            } else {
                // partially uploaded
                $('#jfu_media_file_name'+fileIdStr).val('');
                $('#jfu_media_file_path'+fileIdStr).val('');
                $('#jfu_media_file_partial'+fileIdStr).val(mName);
                percent = parseInt(mSize/originalSize * 100, 10);
            }
            console.log('file found '+mName+ ', ' + mSize + ' uploaded, original size ' + originalSize + ', % = ' + percent );
            setFileStatus(fileId, mName, percent, false);
    } else {
        console.log('file found '+mName+', no info found');
        // smctodo: delete the file?

        $('#jfu_media_file_name'+fileIdStr).val('');
        $('#jfu_media_file_path'+fileIdStr).val('');
        $('#jfu_media_file_partial'+fileIdStr).val('');
    }

    // smctodo: disable select file button, enable Save button
    //j.find('.jfu-file-select').prop('disabled', true);
    //j.find('.fileinput-button').addClass('disabled');
    //$('#btn-save').prop('disabled', false);
    //$('#btn-save').removeClass('disabled');
    //jfu_fileCount = 1;
    //existFileName = mName;
    for (var i=1, file; file=result.files[i]; i++) {
        $('.r-btnAdd').trigger('click');
        //console.log('file '+i+', '+file.name);
        var fileId = i;
        var fileIdStr = '_' + i.toString();
        var mName = file.name;
        var mPath = file.url;
        var mSize = file.size;
        var percent;
        // get the jfu generated temp path from the url
        // e.g. https://.../vendor/jquery-file-upload/server/php/jfu_upload_url/5i5m1svqusapag5ma0tb1tsm15/foobar.png
        mPath = mPath.replace(/^http.*\/jfu_upload_url\//, "");  // smctodo: move this to a config
        console.log(mName, mPath);
       
        var originalSize = localStorage.getItem(mName);
        if (originalSize != null) {
            if (originalSize == mSize) {
                // completely uploaded
                $('#jfu_media_file_name'+fileIdStr).val(mName);
                $('#jfu_media_file_path'+fileIdStr).val(mPath);
                $('#jfu_media_file_partial'+fileIdStr).val('');
                percent = 100;
                msg = '';
                $('#jfu-file-select'+fileIdStr).prop('disabled', true);
                $('#jfu-file-select'+fileIdStr).closest('.fileinput-button').addClass('disabled');
            } else {
                // partially uploaded
                $('#jfu_media_file_name'+fileIdStr).val('');
                $('#jfu_media_file_path'+fileIdStr).val('');
                $('#jfu_media_file_partial'+fileIdStr).val(mName);
                percent = parseInt(mSize/originalSize * 100, 10);
                msg = 'Please select the same file again to resume upload';   
            }
            console.log('file found '+mName+ ', ' + mSize + ' uploaded, original size ' + originalSize + ', % = ' + percent );
            setFileStatus(fileId, mName, percent, false);
        } else {
            console.log('file found '+mName+', no info found');
            // smctodo: delete the file?

            $('#jfu_media_file_name'+fileIdStr).val('');
            $('#jfu_media_file_path'+fileIdStr).val('');
            $('#jfu_media_file_partial'+fileIdStr).val('');
        }


    }
}


/* if file is selected from the server, delete any file uploaded from the client,
and cancel any upload in progress */
$(function(){
    serverSelectChange = function(selectObj) {
        //console.log('this.value '+ $(selectObj).val());
        if ($(selectObj).val() != '') {
            // The id tells which media form is being deleted, 
            // and is needed for clean up.  e.g removing the file from the server

            var a = $(selectObj).attr('id').split('_');
            var idx = a[1];
            // get the input file name (for cleanup if needed)
            var cleanupFile;
            var partialFileName = $('input#jfu_media_file_partial_'+idx).val();
            var completedFileName = $('input#jfu_media_file_name_'+idx).val();
            if (completedFileName != '')
                cleanupFile = completedFileName; 
            else if (partialFileName != '')
                cleanupFile = partialFileName;
            else
                cleanupFile = '';

            console.log('in mediaServerSelect, setting cleanupFile: ' + cleanupFile);
            jfu_customDelete(cleanupFile, idx);
        }
    }
});

var jfu_customDelete = function(cleanupFile, targetId, isCancelFirst) {
    if (cleanupFile != '') {
        cleanupFile = cleanupFile.replace(/\.[^/.]+$/, ""); // remove file extension
        var cancelFileId = '#jfu_cancel_file_' + cleanupFile;
        //console.log('in jfu_customDelete, cancelFileId='+cancelFileId);
        var deleteFileId = '#jfu_delete_file_' + cleanupFile;
        //console.log('in jfu_customDelete, deleteFileId='+deleteFileId);
        // if cancel button exists, cancel upload before delete
        if (targetId !== undefined)
            var id = targetId.toString();
        else {
            var id = null;
            console('in jfu_customDelete , id undefined');
        }
        if (isCancelFirst == null) 
            isCancelFirst = false;
        if (!isCancelFirst && $('button'+deleteFileId).length) {
            /*
            id = $('button'+deleteFileId).closest('table.jfu-presentation').find('input.presentation').attr('id');
            console.log('>>> '+id);
            var tmp = id.split('_');
            id = tmp[1];
            */
            console.log('clearing file status for widget '+id);
            var IdStr = '_'+id;
            $('#jfu_media_file_name'+IdStr).val('');
            $('#jfu_media_file_path'+IdStr).val('');        
            $('#jfu_media_file_partial'+IdStr).val('');
            setFileStatus(id, '');
            $('button'+deleteFileId).trigger('click');
            console.log('-Clicked button deleteFileId='+deleteFileId);
        } else {
            //console.log('delete button not found.  Look for cancel button to click, then delete again.');
            if ($('button'+cancelFileId).length) {
                console.log('--cancel button found, canceling upload...');
                $('button'+cancelFileId).trigger('click');

                setTimeout(function(){ 
                    //console.log('in settimeout waiting..., about to click on delete button ');
                    if ($('button'+deleteFileId).length) {
                        /*
                        id = $('button'+deleteFileId).closest('table.jfu-presentation').find('input.presentation').attr('id');
                        var tmp = id.split('_');
                        id = tmp[1];
                        */
                        console.log('--clearing file status for widget '+id);
                        var IdStr = '_'+id;
                        $('#jfu_media_file_name'+IdStr).val('');
                        $('#jfu_media_file_path'+IdStr).val('');        
                        $('#jfu_media_file_partial'+IdStr).val('');
                        setFileStatus(id, '');
                        $('button'+deleteFileId).trigger('click');
                        console.log('--Clicked button deleteFileId='+deleteFileId);
                    }
                }, 500);
            } else {
                console.log('cancel button not found');
            }
        }
    } else {
        console.log('cleanupFile arg is empty');
    }
    
}

var jfu_customCancel = function(cleanupFile, targetId) {
    if (cleanupFile != '') {
        // cancel upload, then delete the file
        jfu_customDelete(cleanupFile, targetId, true);
    } else {
        console.log('cleanupFile arg is empty');
    }
}

var btnSaveClick = function(formName) {
    // For each form, either the client files must be uploaded completely, 
    // or a file must be selected for server upload.  If either case fails, do not allow the form to be saved.
    var msg = '';
    if ($('select#media_0_mediaServerPath').length === 0) { 
        // no drop down : meaning server upload is not available
        var isServerUploadAvailable = false;
    } else {
        var isServerUploadAvailable = true;
    }
    console.log('in btnSaveClick, jfu_widgetCount=' + jfu_widgetCount);
    var clientUploadReady = true;
    var serverUploadReady = true;
    for (var i=0; i<jfu_widgetCount; i++) {
        // Check each widget, if the file name or temp path not set, client upload is not ready
        if ($('#jfu_media_file_name_'+i).val() === '' ||
            $('#jfu_media_file_path_'+i).val() === '' ) {
            clientUploadReady = false;    
        }
        if (isServerUploadAvailable) {
            // if no file selected in the drop down, server upload is not ready
            if ($('select#media_'+i+'_mediaServerPath').val() === '') {
                serverUploadReady = false;    
            }
            
        } else {
            serverUploadReady = false;    

        }
        if (!clientUploadReady && !serverUploadReady) {
            msg = 'Please select the file to be uploaded for each media, and make sure the file is uploaded completely. ';
            if (isServerUploadAvailable)
                msg += 'For uploading from the server, please make sure the file is selected from the drop-down menu.';
        }
        
    }
    /*
    if (msg === '') {
        if ($('select[name="mediaServerPath"]').length === 0) {
            // Server upload not available, check only if client upload is ready
            if (!clientUploadReady) {
                msg = 'Please select a file from your computer, and make sure the file is uploaded completely.';
            }
        } else {
            if ($('select[name="mediaServerPath"]').val() !== '') {
                serverUploadReady = true;    
            }
            if (clientUploadReady && serverUploadReady) {
                msg = 'You have selected a file from your computer, and also a file from the server. Please remove one.';
            } else if (!clientUploadReady && !serverUploadReady) {
                msg = 'Please select a file from your computer, and make sure the file is uploaded completely.  Otherwise, you can choose a file from the server, then click Save.';
            }
        }
    } */
    if (formName === 'mediaForm') {
        if(!jQuery('#msFacilityID').val() || !jQuery('#title').val()){
            msg = "Please enter the description and select a facility";
        }
    }
    if (msg  === '') {
        $('.btn-save').prop('disabled', true);
        $('.btn-save').addClass('disabled');
        //alert('ok to submit ' + formName);
        $('#'+formName).submit();
        console.log('mediaform length='+$('#mediaForm').length);
        //document.getElementById('mediaForm').submit();
        //return true;
    } else {
        alert(msg);
        return false;        
    }
}

