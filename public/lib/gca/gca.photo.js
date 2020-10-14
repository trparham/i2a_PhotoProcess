gca.namespace("photo");
gca.photo = (function (w, d, $) {

    downloadUrl = '/image/download';

    var initialize = function () {
    	console.log('initialize photo');
        initializeButtons();
        initializeFileUpload();
    },
    initializeButtons = function () {
    	console.log('initializeButtons photo');
        $('#processButton').off("click").on("click", function (e) {
            e.preventDefault();
            onProcessClick2();
        });
        
        $('#finalButton').off("click").on("click", function (e) {
            e.preventDefault();
            finalizeImage();
        });
    },
    initializeFileUpload = function () {
    	console.log('initializeFileUpload');
        var url = '/photo/uploadimage';

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
                        //$('.image-original img').attr("src", file.original_file);
                        $('#originalImage').val(file.final_file);
                        $("#prevOriginal img").attr("src", file.final_file);
                        $("#prevOriginal p.help-block").html("New Image");
                    }
                    console.log('call numberAction');
                    callNumber(file.final_file);
                });
                console.log('new image');
            }
        });
    }
    
    callNumber = function(fileName) {
    	console.log('callNumber2 ' + fileName);

        $.ajax({
            url: '/photo/number',
            method: 'post',
            dataType: 'json',
            data: {
            	image: fileName,
        	},
            success: function (data, status, jqXHR) {

                console.log('processing complete');
                console.log(data);
                $('#loading').hide();

                // Maintain original image
                $('#originalImage').val(data.originalImage);
                
                // Numbered image
                $('#numberedImage').val(data.numberedImage);
                $("#prevNumbered img").attr("src", data.numberedImage);
                $("#prevNumbered p.help-block").html("Numbered Image");
            },
            error: function (xhr, status, error) {
                console.log('/photo/number failed');
                console.log(xhr);
                console.log(status);
                console.log(error);
            }
        });
    };
    
    onProcessClick2 = function () {
    	console.log('onProcessClick2');
    	console.log(new Date().getTime());
        $.ajax({
            url: '/photo/preview',
            method: 'post',
            dataType: 'json',
            data: {
            	image: $("#originalImage").val(),
            	palette: $("input[name=colorPalette]:checked").val()
        	},
            success: function (data, status, jqXHR) {

                console.log('processing complete');
                console.log(data);

                // Maintain original image
                $('#originalImage').val(data.originalImage);
                
                console.log('ref = ' + data.referenceImage);
                // Reference image
                $("#prevReference img").attr("src", '');
                $('#referenceImage').val(data.referenceImage);
                $("#prevReference img").attr("src", data.referenceImage + '?' + new Date().getTime());
                $("#prevReference p.help-block").html("Reference Image");
                console.log(new Date().getTime());
                console.log('done');

            },
            error: function (xhr, status, error) {
                console.log('nope');
            }
        });

    };
    
    finalizeImage = function () {
    	console.log('finalizeImage');
        $.ajax({
            url: '/photo/finalize',
            method: 'post',
            dataType: 'json',
            data: {
            	image: $("#originalImage").val(),
            	finalFileName: $("#finalFileName").val(),
            	microsite: $("#microsite").val(),
            	venue: $("#venue").val(),
            	eventTime: $("#eventTime").val()
        	},
            success: function (data, status, jqXHR) {
                console.log('finalize complete');
                console.log(data);
            },
            error: function (xhr, status, error) {
                console.log('error in finalize');
            }
        });

    };
    
	// Initializations
    return  {
        initialize: initialize
    };
}(window, document, jQuery));

