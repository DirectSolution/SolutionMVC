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

    $('forgotten_pass').submit(function (event) {
        $('.alert-danger').remove();
        $('.alert-success').remove();
        var formData = {
            '_username': $('input[name=_username]').val(),
            '_client': $('input[name=_client]').val(),
            '_password': $('input[name=_password]').val(),
            '_password_second': $('input[name=_password_second]').val()
        };
        $.ajax({
            type: 'POST',
            url: 'http://doug.portal.solutionhost.co.uk/apps2/public/portal/Login/ForgottenPassword/ajax',
            data: formData,
            dataType: 'json',
            encode: true
        })
                .done(function (data) {
                    if (data.status === "success") {
                        $("#forgotForm").before("<div class='alert alert-success' role='alert'>" + data.message + "</div>");
                    } else if (data.status === "error"){
                        $("#forgotForm").before("<div class='alert alert-danger' role='alert'>" + data.message + "</div>");
                    }
                });
        event.preventDefault();
    });
});