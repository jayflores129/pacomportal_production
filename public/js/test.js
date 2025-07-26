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

$(function () {
    'use strict';


    var formData = $('form').serializeArray();

    $('#fileupload').bind('fileuploadsubmit', function (e, data) {
        // The example input, doesn't have to be part of the upload form:

        console.log('testsasdfs');

        var input = $('#selectCat');
        var files = $('#files');
  console.log( 'test ' + data);

        data.formData = {
            category: input.val(),
            file: files.val(),
    
        };

        $('#files').each(function () {
            $(this).fileupload({
                fileInput: $(this).find('input:file')
            });
        });


        // $.fn.serializefiles = function() {
        //     var obj = $(this);
        //     /* ADD FILE TO PARAM AJAX */
        //     var formData = new FormData();
        //     $.each($(obj).find("input[type='file']"), function(i, tag) {
        //         $.each($(tag)[0].files, function(i, file) {
        //             formData.append(tag.name, file);
        //         });
        //     });
        //     var params = $(obj).serializeArray();
        //     $.each(params, function (i, val) {
        //         formData.append(val.name, val.value);
        //     });
        //     return formData;
        // };

  
            // headers: {
            //     'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            //     'Content-type': 'text/plain'
            // }


                   // Load existing files:
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
                    $(this).fileupload('option', 'done')
                        .call(this, $.Event('done'), {result: result});
                });



            
          });


        // if (!data.formData.category) {
        //   data.context.find('button').prop('disabled', false);
        //   input.focus();
        //   return false;
        // }

    });







    //Initialize the jQuery File Upload widget:
    // $('#fileupload').fileupload({
    //     // Uncomment the following to send cross-domain cookies:
    //     //xhrFields: {withCredentials: true},
    //     //url: 'server/php/'//localhost:8000/onedrive/public/files
    //     url: '//localhost/onedrive/public/files',
    //     maxFileSize: 999000,
    //     acceptFileTypes: /(\.|\/)(gif|jpe?g|png)$/i,

    // });

    // // Enable iframe cross-domain access via redirect option:
    // $('#fileupload').fileupload(
    //     'option',
    //     'redirect',
    //     window.location.href.replace(
    //         /\/[^\/]*$/,
    //         '/cors/result.html?%s'
    //     )
    // );

    // if (window.location.hostname === 'blueimp.github.io') {
    //     // Demo settings:
    //     $('#fileupload').fileupload('option', {
    //         url: '//jquery-file-upload.appspot.com/',
    //         // Enable image resizing, except for Android and Opera,
    //         // which actually support image resizing, but fail to
    //         // send Blob objects via XHR requests:
    //         disableImageResize: /Android(?!.*Chrome)|Opera/
    //             .test(window.navigator.userAgent),
    //         maxFileSize: 999000,
    //         acceptFileTypes: /(\.|\/)(gif|jpe?g|png)$/i
    //     });
    //     // Upload server status check for browsers with CORS support:
    //     if ($.support.cors) {
    //         $.ajax({
    //             url: '//jquery-file-upload.appspot.com/',
    //             type: 'HEAD'
    //         }).fail(function () {
    //             $('<div class="alert alert-danger"/>')
    //                 .text('Upload server currently unavailable - ' +
    //                         new Date())
    //                 .appendTo('#fileupload');
    //         });
    //     }
    // } else {
    





    //}

// });