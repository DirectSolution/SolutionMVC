$(document).ready(function () {

    $("#client").keyup(function (e) {
        var client = $("#client");
        var empty = $.trim($("#client").val());
        if (!client.val().match(/^\d+$/) && empty.length > 0) {
            $('#client_group').addClass("has-error");
            $('#client_error').show();
        } else {
            $('#client_group').removeClass("has-error");
            $('#client_error').hide();
        }
    });

    $('#logmein').submit(function (event) {
        event.preventDefault();
        if (location.pathname !== "/apps2/public/Portal/Login") {
//            alert(location.pathname);
            var url = location.pathname;
        } else {
            var url = null;
        }
        var $form = $(this);
        var $oldaction = $form.attr('action');
        var $action = $oldaction.replace("html", "ajax");

//        alert($action);

        $('.alert-danger').remove();
        var formData = {
            '_username': $('input[name=_username]').val(),
            '_client': $('input[name=_client]').val(),
            '_password': $('input[name=_password]').val()
        };
        $.ajax({
            type: 'POST',
            url: $action,
            data: formData,
            dataType: 'json',
            encode: true
        })
                .done(function (data) {
                    if (data.status === "success") {
                        
                        if (data.url !== null) {
                            
                            window.location = data.url;
                            console.log(data.url);
                        } 
//                        else {
//                            window.location = url;
//                        }
                    } else {
                        $("#client_group").before("<div class='alert alert-danger' role='alert'>" + data.message + "</div>");
                    }
                });

    });
});