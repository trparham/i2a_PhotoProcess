var gca = gca || {};

gca.namespace = function (namespace) {
    var parts = namespace.split('.'),
            length = parts.length,
            parent = gca,
            i = 0;
    if (parts[0] === "gca") {
        i += 1;
    }
    for (; i < length; i += 1) {
        if (typeof parent[parts[i]] === "undefined") {
            parent[parts[i]] = {};
        }
        parent = parent[parts[i]];
    }
    return parent;
};


//$.validator.setDefaults({
//    errorElement: "span",
//    errorClass: "help-block",
//    highlight: function (element, errorClass, validClass) {
//        $(element).closest('.form-group').addClass('has-error');
//    },
//    unhighlight: function (element, errorClass, validClass) {
//        $(element).closest('.form-group').removeClass('has-error');
//    },
//    errorPlacement: function (error, element) {
//        if (element.parent('.input-group').length || element.prop('type') === 'checkbox' || element.prop('type') === 'radio') {
//            error.insertAfter(element.parent());
//        } else {
//            error.insertAfter(element);
//        }
//    }
//});

$.ajaxSetup({
    // Disable caching of AJAX responses
    cache: false
});

/**
 * Display message dialog.
 */
//displayMessage = function (message, title) {
//    // Set title
//    if (!title) {
//        title = 'Unexpected Error';
//    }
//
//    // Set message
//    $('#messageDialog span').html(message);
//
//    // Display
//    $('#messageDialog').dialog({
//        modal: true,
//        width: 475,
//        title: title,
//        buttons: {
//            OK: function () {
//                $('#messageDialog').dialog('close');
//            }
//        },
//        close: function () {
//            $('#messageDialog span').empty();
//        }
//    });
//};

/**
 * Display confirmation dialog.  Execute callback if user confirms.
 */
displayConfirm = function (message, confirmAction) {
    // Set message
    $('#messageDialog span').html(message);

    // Display
    $('#messageDialog').dialog({
        modal: true,
        title: 'Confirm Action',
        buttons: {
            OK: function () {
                $('#messageDialog').dialog('close');
                if (confirmAction) {
                    confirmAction();
                }
            },
            Cancel: function () {
                $('#messageDialog').dialog('close');
            }
        },
        close: function () {
            $('#messageDialog span').empty();
        }
    });
};

//$("#content").dialog({
//    width: 'auto', // overcomes width:'auto' and maxWidth bug
//    maxWidth: 600,
//    height: 'auto',
//    modal: true,
//    fluid: true, //new option
//    resizable: false
//});


// on window resize run function
$(window).resize(function () {
    fluidDialog();
});

// catch dialog if opened within a viewport smaller than the dialog width
$(document).on("dialogopen", ".ui-dialog", function (event, ui) {
    fluidDialog();
});

function fluidDialog() {
    var $visible = $(".ui-dialog:visible");
    // each open dialog
    $visible.each(function () {
        var $this = $(this);
        var dialog = $this.find(".ui-dialog-content").data("ui-dialog");
        // if fluid option == true
        if (dialog.options.fluid) {
            var wWidth = $(window).width();
            // check window width against dialog width
            if (wWidth < (parseInt(dialog.options.maxWidth) + 50)) {
                // keep dialog from filling entire screen
                $this.css("max-width", "90%");
            } else {
                // fix maxWidth bug
                $this.css("max-width", dialog.options.maxWidth + "px");
            }
            //reposition dialog
            dialog.option("position", dialog.options.position);
        }
    });
}

window.onload = function () {
    if (navigator.userAgent.indexOf('Safari') !== -1 && navigator.userAgent.indexOf('Chrome') === -1) {
        var cookies = document.cookie;

        if (top.location !== document.location) {
            var rtn = document.referrer.indexOf('return=');
            if (!cookies && rtn === -1) {
                href = document.location.href;
                href = (href.indexOf('?') === -1) ? href + '?' : href + '&';
                top.location.href = href + 'reref=' + encodeURIComponent(document.referrer);
            }
        } else {
            ts = new Date().getTime();
            document.cookie = 'ts=' + ts;

            rerefidx = document.location.href.indexOf('reref=');
            if (rerefidx !== -1) {
                href = decodeURIComponent(document.location.href.substr(rerefidx + 6));
                href = (href.indexOf('?') === -1) ? href + '?' : href + '&';
                href = href + 'return=1';
                window.location.replace(href);
            }
        }
    }
};

Date.prototype.stdTimezoneOffset = function () {
    var jan = new Date(this.getFullYear(), 0, 1);
    var jul = new Date(this.getFullYear(), 6, 1);
    return Math.max(jan.getTimezoneOffset(), jul.getTimezoneOffset());
};

Date.prototype.dst = function () {
    return this.getTimezoneOffset() < this.stdTimezoneOffset();
};;
gca.namespace("image");
gca.image = (function (w, d, $) {

    downloadUrl = '/image/download';

    var initialize = function () {
    	console.log('initialize');
//        initializeForms();
        initializeButtons();
        initializeFileUpload();
    },
            initializeButtons = function () {

                $('#btnGray').off("click").on("click", function (e) {
                    e.preventDefault();
                    onGrayscaleClick();
                });
                $('#btnPoster').off("click").on("click", function (e) {
                    e.preventDefault();  
                    onPosterizeClick();
                });
                $('#btnFinish').off("click").on("click", function (e) {
                    e.preventDefault();
                    onFinishClick();
                });
                $('#btnResize').off("click").on("click", function (e) {
                    e.preventDefault();
                    onResizeClick();
                });
                $('#btnDownload').off("click").on("click", function (e) {
                    e.preventDefault();
                    onDownloadClick();
                });
                $('#btnBrtInc').off("click").on("click", function (e) {
                    e.preventDefault();
                    onBrtIncrease();
                });
                $('#btnBrtDec').off("click").on("click", function (e) {
                    e.preventDefault();
                    onBrtDecrease();
                });
                $('#btnConInc').off("click").on("click", function (e) {
                    e.preventDefault();
                    onConIncrease();
                });
                $('#btnConDec').off("click").on("click", function (e) {
                    e.preventDefault();
                    onConDecrease();
                });
                $('#btnShpInc').off("click").on("click", function (e) {
                    e.preventDefault();
                    onShpIncrease();
                });
                $('#btnShpDec').off("click").on("click", function (e) {
                    e.preventDefault();
                    onShpDecrease();
                });
                $('#btnLODDec').off("click").on("click", function (e) {
                    e.preventDefault();
                    onBlrIncrease();
                });
                $('#btnLODInc').off("click").on("click", function (e) {
                    e.preventDefault();
                    onBlrDecrease();
                });

//        $('#btnRevert').click(onRevertClick);
            },
//    initializeForms = function () {
//        $('form.validate').validate();  
//        $("form.validate").dirtyForms();
//
//        $("form.validate").dirtyForms('setClean');
//    },
            initializeFileUpload = function () {
            	console.log('initializeFileUpload');
                var url = '/image/uploadimage/upload';

                $('#fileupload').fileupload({//Upload Image button
                    url: url,
                    dataType: 'json',
                    done: function (e, data) {
                    	console.log('done');
                    	console.log(e);
                    	console.log(data);
                        $('.number-val').val(0);
                        $('.detail-val').val(3);
                        $('#btnLODInc').addClass('disabled', true);
                        console.log('done with upload');
                        $('#loading').hide();

                        $.each(data.result, function (index, file) {
                        	console.log('trp here');
                        	console.log(index);
                        	console.log(file);
                            if (file.status == 200) {
                                $('.image-original img').attr("src", file.original_file);
                                $('#image').val(file.final_file);
                                $(".image-preview img").attr("src", file.final_file);
                                $(".image-preview p.help-block").html("New Image");
                            }
                        });
                        console.log('new image');
                    }
//                    
                });//.prop('disabled', !$.support.fileInput).parent().addClass($.support.fileInput ? undefined : 'disabled');
            },
            onResizeClick = function () {
                $('#loading').show();
                $.ajax({
                    url: '/image/resizeImage',
                    method: 'post',
                    dataType: 'json',
                    data: {imgPath: $("#image").val()},
                    success: function (data, status, jqXHR) {

                        console.log('resizing complete');
                        console.log(data);
                        $('#loading').hide();

                        $('#image').val(data.final_file);
                        $(".image-preview img").attr("src", data.final_file);
                        $(".image-preview p.help-block").html("Resized Image");
                    },
                    error: function (xhr, status, error) {
                        console.log('nope');
                    }
                });

            };
    onGrayscaleClick = function () {
        $('#loading').show();
        $.ajax({
            url: '/image/grayscaleImage',
            method: 'post',
            dataType: 'json',
            data: {imgPath: $("#image").val()},
            success: function (data, status, jqXHR) {
                console.log('grayscale complete');
                console.log(data);
                $('#loading').hide();

                $('#image').val(data.final_file);
                $(".image-preview img").attr("src", data.final_file);
                $(".image-preview p.help-block").html("Grayscale Image");
            },
            error: function (xhr, status, error) {
                console.log('nope');
            }
        });

    };
    onPosterizeClick = function () {
        $('#loading').show();
        $.ajax({
            url: '/image/posterizeImage',
            method: 'post',
            dataType: 'json',
            data: {imgPath: $("#image").val()},
            success: function (data, status, jqXHR) {
                console.log('posterize complete');
                console.log(data);
                $('#loading').hide();

                $('#image').val(data.final_file);
                $(".image-preview img").attr("src", data.final_file);
                $(".image-preview p.help-block").html("Posterized Image");
            },
            error: function (xhr, status, error) {
                console.log('nope');
            }
        });
    };
    onFinishClick = function () {
        $('#loading').show();
        $.ajax({
            url: '/image/finishImage',
            method: 'post',
            dataType: 'json',
            data: {imgPath: $("#image").val()},
            success: function (data, status, jqXHR) {
                console.log('finish complete');
                console.log(data);
                $('#loading').hide();

                $('#image').val(data.final_file);
                $(".image-preview img").attr("src", data.final_file);
                $(".image-preview p.help-block").html("Numbered Image");
            },
            error: function (xhr, status, error) {
                console.log('nope');
            }
        });

    };
    onDownloadClick = function () {
        $('#imageEdit').attr('action', downloadUrl);
        $("#image").val();
        $('#imageEdit').submit();
        return;
    };
    onBrtIncrease = function () {
        $('#loading').show();
        $.ajax({
            url: '/image/adjustImage/brtinc',
            method: 'post',
            dataType: 'json',
            data: {imgPath: $("#image").val()},
            success: function (data, status, jqXHR) {
                console.log('brightness +');
                var $amt = $('#btnBrtInc').closest('div').find('.number-val');
                var currentVal = parseInt($amt.val());
                if ($.isNumeric(currentVal)) {
                    $amt.val(currentVal + 1);
                }
                $('#loading').hide();
                d = new Date();
                $('#image').val(data.original_file);
                $(".image-preview img").attr("src", data.final_file + '?x=' + d.getTime());

            },
            error: function (xhr, status, error) {
                console.log('nope');
            }
        });
    };
    onBrtDecrease = function () {
        $('#loading').show();
        $.ajax({
            url: '/image/adjustImage/brtdec',
            method: 'post',
            dataType: 'json',
            data: {imgPath: $("#image").val()},
            success: function (data, status, jqXHR) {
                console.log('brightness -');
                var $amt = $('#btnBrtDec').closest('div').find('.number-val');
                var currentVal = parseInt($amt.val());
                if ($.isNumeric(currentVal)) {
                    $amt.val(currentVal - 1);
                }
                $('#loading').hide();
                d = new Date();
                $('#image').val(data.original_file);
                $(".image-preview img").attr("src", data.final_file + '?x=' + d.getTime());
            },
            error: function (xhr, status, error) {
                console.log('nope');
            }
        });
    };
    onConIncrease = function () {
        $('#loading').show();
        $.ajax({
            url: '/image/adjustImage/coninc',
            method: 'post',
            dataType: 'json',
            data: {imgPath: $("#image").val()},
            success: function (data, status, jqXHR) {
                console.log('contrast +');
                var $amt = $('#btnConInc').closest('div').find('.number-val');
                var currentVal = parseInt($amt.val());
                if ($.isNumeric(currentVal)) {
                    $amt.val(currentVal + 1);
                }
                $('#loading').hide();
                d = new Date();
                $('#image').val(data.original_file);
                $(".image-preview img").attr("src", data.final_file + '?x=' + d.getTime());
            },
            error: function (xhr, status, error) {
                console.log('nope');
            }
        });
    };
    onConDecrease = function () {
        $('#loading').show();
        $.ajax({
            url: '/image/adjustImage/condec',
            method: 'post',
            dataType: 'json',
            data: {imgPath: $("#image").val()},
            success: function (data, status, jqXHR) {
                console.log('contrast -');
                var $amt = $('#btnConDec').closest('div').find('.number-val');
                var currentVal = parseInt($amt.val());
                if ($.isNumeric(currentVal)) {
                    $amt.val(currentVal - 1);
                }
                $('#loading').hide();
                d = new Date();
                $('#image').val(data.original_file);
                $(".image-preview img").attr("src", data.final_file + '?x=' + d.getTime());
            },
            error: function (xhr, status, error) {
                console.log('nope');
            }
        });
    };
    onShpIncrease = function () {
        $('#loading').show();
        $.ajax({
            url: '/image/adjustImage/shpinc',
            method: 'post',
            dataType: 'json',
            data: {imgPath: $("#image").val()},
            success: function (data, status, jqXHR) {
                console.log('sharpness +');
                var $amt = $('#btnShpInc').closest('div').find('.number-val');
                var currentVal = parseInt($amt.val());
                if ($.isNumeric(currentVal)) {
                    $amt.val(currentVal + 1);
                }
                $('#loading').hide();
                d = new Date();
                $('#image').val(data.original_file);
                $(".image-preview img").attr("src", data.final_file + '?x=' + d.getTime());
            },
            error: function (xhr, status, error) {
                console.log('nope');
            }
        });
    };
    onShpDecrease = function () {
        $('#loading').show();
        $.ajax({
            url: '/image/adjustImage/shpdec',
            method: 'post',
            dataType: 'json',
            data: {imgPath: $("#image").val()},
            success: function (data, status, jqXHR) {
                console.log('sharpness -');
                var $amt = $('#btnShpDec').closest('div').find('.number-val');
                var currentVal = parseInt($amt.val());
                if ($.isNumeric(currentVal)) {
                    $amt.val(currentVal - 1);
                }
                $('#loading').hide();
                d = new Date();
                $('#image').val(data.original_file);
                $(".image-preview img").attr("src", data.final_file + '?x=' + d.getTime());
            },
            error: function (xhr, status, error) {
                console.log('nope');
            }
        });
    };
    onBlrIncrease = function () {
        $('#loading').show();
        $.ajax({
            url: '/image/adjustImage/blrinc',
            method: 'post',
            dataType: 'json',
            data: {imgPath: $("#image").val()},
            success: function (data, status, jqXHR) {
                console.log('blur +');
                var $amt = $('#btnLODDec').closest('div').find('.detail-val');
                var currentVal = parseFloat($amt.val());
                if ($.isNumeric(currentVal)) {
                    $amt.val(currentVal - 1);
                }
                var maxLvl = 3;
                var minLvl = 1;
                var newVal = parseFloat($amt.val());
                if (parseFloat(newVal) < maxLvl){
                    $('#btnLODInc').removeClass('disabled', true);
                }
                if (parseFloat(newVal) == minLvl){
                    $('#btnLODDec').addClass('disabled', true);
                }
                $('#loading').hide();
                d = new Date();
                $('#image').val(data.original_file);
                $(".image-preview img").attr("src", data.final_file + '?x=' + d.getTime());
            },
            error: function (xhr, status, error) {
                console.log('nope');
            }
        });
    };
    onBlrDecrease = function () {
        $('#loading').show();
        $.ajax({
            url: '/image/adjustImage/blrdec',
            method: 'post',
            dataType: 'json',
            data: {imgPath: $("#image").val()},
            success: function (data, status, jqXHR) {
                console.log('blur -');
                var $amt = $('#btnLODInc').closest('div').find('.detail-val');
                var currentVal = parseFloat($amt.val());
                if ($.isNumeric(currentVal)) {
                    $amt.val(currentVal + 1);
                }
                var maxLvl = 3;
                var minLvl = 1;
                var newVal = parseFloat($amt.val());
                if (parseFloat(newVal) > minLvl){
                    $('#btnLODDec').removeClass('disabled', true);
                }
                if (parseFloat(newVal) == maxLvl){
                    $('#btnLODInc').addClass('disabled', true);
                }
                $('#loading').hide();
                d = new Date();
                $('#image').val(data.original_file);
                $(".image-preview img").attr("src", data.final_file + '?x=' + d.getTime());
            },
            error: function (xhr, status, error) {
                console.log('nope');
            }
        });
    };
//    onRevertClick = function (e) {
//        e.preventDefault();
//        $('form.validate').dirtyForms('setClean'); 
//    };
    return  {
        initialize: initialize
    };
}(window, document, jQuery));

