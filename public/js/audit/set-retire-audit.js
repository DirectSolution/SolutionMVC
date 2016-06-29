$(document).on('click', '.setRetire', function (event) {
    var id = $(this).val();

    $.ajax({
        type: 'POST',
        url: "http://doug.portal.solutionhost.co.uk/apps2/public/Audit/Audit/retire",
        data: {
            id: id
        },
        dataType: 'json',
        encode: true,
        async: false
    })
            .then(function () {
//                if(data.status === "success"){
//                    $("#client_group").before("<div class='alert alert-success' role='alert'>" + data.message + "</div>");
//                }else{
//                    $("#client_group").before("<div class='alert alert-danger' role='alert'>" + data.message + "</div>");
//                }
                
                alert("this");
                alert(this);
                $(this).parent().parent().parent().parent().parent().parent().parent().remove();

            });

});
