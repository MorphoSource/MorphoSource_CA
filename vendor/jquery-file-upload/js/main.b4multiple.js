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

var maxFiles = 1;
var fileCount = 0;
var existFileName = '';

$(function () {
    'use strict';
    var handlerUrl = '/vendor/jquery-file-upload/server/php/';
    
    // Initialize the jQuery File Upload widget:
    $('#fileupload').fileupload({
        // Uncomment the following to send cross-domain cookies:
        //xhrFields: {withCredentials: true},
        url: handlerUrl
    });
    $("#fileupload").bind("fileuploaddone", function (e, data) {
        console.log('fileuploaddone');
        /* get the upload file name and temp upload path from the response. Basemodel will
        use the paths for copying the file over to the destination dir */
        //console.log("fileuploaddone event fired , data=", data);
        var mName = data.result.files[0].name;
        var mPath = data.result.files[0].url;
        // get the jfu generated temp path from the url
        // e.g. https://.../vendor/jquery-file-upload/server/php/jfu_upload_url/5i5m1svqusapag5ma0tb1tsm15/foobar.png
        mPath = mPath.replace(/^http.*\/jfu_upload_url\//, "");  // smctodo: move this to a config
        //console.log(mName, mPath);
        $('#jfu_media_file_name').val(mName);
        $('#jfu_media_file_path').val(mPath);
        // smctodo: disable select file button, enable Save button
        $('#jfu-file-select').prop('disabled', true);
        $('.fileinput-button').addClass('disabled');
        //$('#btn-save').prop('disabled', false);
        //$('#btn-save').removeClass('disabled');
        fileCount = 1;
        existFileName = mName;
    });
    $('#fileupload').bind('fileuploaddestroyed', function (e, data) {
        // after file is deleted, clear file name and temp path
        $('#jfu_media_file_name').val('');
        $('#jfu_media_file_path').val('');        
        // smctodo: enable select file button, disable Save button
        $('#jfu-file-select').prop('disabled', false);
        $('.fileinput-button').removeClass('disabled');
        //$('#btn-save').prop('disabled', true);
        //$('#btn-save').addClass('disabled');
        fileCount = 0;
        existFileName = '';
    });

    // Enable iframe cross-domain access via redirect option:
    $('#fileupload').fileupload(
        'option',
        'redirect',
        window.location.href.replace(
            /\/[^\/]*$/,
            '/cors/result.html?%s'
        )
    );

    
    $('#fileupload').fileupload('option', {
        url: handlerUrl,
        acceptFileTypes: /(\.|\/)(gif|jpe?g|png|zip|csv)$/i,
        //maxFileSize: 9,
        maxChunkSize: 1000000, // 1 MB
        autoUpload: true,
        disableImageResize:true,
        previewThumbnail:false,
        maxNumberOfFiles:1,
        // resuming file uploads: https://github.com/blueimp/jQuery-File-Upload/wiki/Chunked-file-uploads
        add: function (e, data) {
            console.log('smc: in add function : counter:', fileCount);
            var that = this;
            $.getJSON(handlerUrl, {file: data.files[0].name}, function (result) {
                var file = result.file;
                data.uploadedBytes = file && file.size;
                //data.uploadedBytes = file && Number(file.size);
                //console.log('uploadedbytes: ',data.uploadedBytes);
                //console.log('file size: ', data.files[0].size);
                // before upload or resume upload, check if there is an existing file (partial or completely uploaded) 
                if (fileCount >= maxFiles) {
                    if (data.files[0].name !== existFileName) {
                        // user tries to upload a different file
                        alert('Please delete existing file if you would like to upload another file.' + data.files[0].name);
                        return false;
                    } else if (data.files[0].size === data.uploadedBytes) {
                        // user tries to upload the same file, but the file is completely uploaded already
                        alert('The file is already uploaded.  Please fill out the rest of the information and click Save');
                        return false;
                    }
                }
                // disable upload button before upload or resume upload
                $('#jfu-file-select').prop('disabled', true);
                $('.fileinput-button').addClass('disabled');
                // clear upload from server select dropdown
                $('select[name="mediaServerPath"]').val('');

                if ($('.template-download').length === 1) {
                    // delete the old download template if it exists
                    $('.template-download').remove();
                }
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
            }
            data.context.removeData('retries');
            $.blueimp.fileupload.prototype
                .options.fail.call(this, e, data);

            // BEGIN: Load existing files after canceling an upload
            $('#fileupload').addClass('fileupload-processing');
            $.ajax({
                // Uncomment the following to send cross-domain cookies:
                //xhrFields: {withCredentials: true},
                url: $('#fileupload').fileupload('option', 'url'),
                dataType: 'json',
                context: $('#fileupload')[0]
            }).always(function () {
                $(this).removeClass('fileupload-processing');
            }).done(function (result) {
                fileCount = result.files.length;
                //console.log('load existing files, return files count:',fileCount);
                if (fileCount === 1) {
                    existFileName = result.files[0].name;
                    //console.log(existFileName);
                }
                // disable upload button if there is existing file
                //$('#jfu-file-select').prop('disabled', true);        
                $(this).fileupload('option', 'done')
                    .call(this, $.Event('done'), {result: result});
            });
            // END: Load existing files after canceling an upload                
            
            
            // enable upload button after canceling 
            $('#jfu-file-select').prop('disabled', false);
            $('.fileinput-button').removeClass('disabled');
            
        }            
    });

    // Load existing files on page load:
    $('#fileupload').addClass('fileupload-processing');
    $.ajax({
        // Uncomment the following to send cross-domain cookies:
        //xhrFields: {withCredentials: true},
        url: $('#fileupload').fileupload('option', 'url'),
        dataType: 'json',
        context: $('#fileupload')[0]
    }).always(function () {
        $(this).removeClass('fileupload-processing');
    }).done(function (result) {
        fileCount = result.files.length;
        console.log('load existing files, return files count:',fileCount);
        if (fileCount === 1) {
            existFileName = result.files[0].name;
            console.log(existFileName);
        }
        // disable upload button if there is existing file
        //$('#jfu-file-select').prop('disabled', true);        
        $(this).fileupload('option', 'done')
            .call(this, $.Event('done'), {result: result});
    });

});

var btnSaveClick = function() {
    console.log('clicked save');
    var msg = '';
    var clientUploadReady = false;
    var serverUploadReady = false;
    if ($('#jfu_media_file_name').val() !== '' &&
        $('#jfu_media_file_path').val() !== '') {
        clientUploadReady = true;    
    } else{
        // check if a partial file has been uploaded 
        if (existFileName !== '') {
            msg = 'There is an existing file partially uploaded.  Please finish the upload or delete the existing file if you would like to upload a new file.';
        };
        
    }
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
    }
    if (msg  === '') {
        $('#btn-save').prop('disabled', true);
        $('#btn-save').addClass('disabled');
        //alert('ok to submit');
        $("#mediaFilesForm").submit();
        
    } else {
        alert(msg);
        return false;
        
    }
}

