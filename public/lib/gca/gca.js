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

$.ajaxSetup({
    // Disable caching of AJAX responses
    cache: false
});

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