$('#retireAssignmentModal').on('show.bs.modal', function (event) {

    var modal = $(this);
    var button = $(event.relatedTarget); // Button that triggered the modal
    var assId = button.attr('data-assignment'); // Extract info from data-* attributes

    modal.find('#retireNow').val(assId);
    modal.find('#leadText').append("<i>Are you sure, you would like to remove this assignment?</i>");
    modal.find('#assetID').val(assId);
});

$('body').on('click', '#retireNow', function (e) {
    $.ajax({
        type: 'POST',
        url: "//doug.portal.solutionhost.co.uk/apps2/public/Audit/Assignment/retireAssignment",
        dataType: 'json',
        data: {
            "id": $(this).val()
        },
        encode: true
    })
            .done(function (data) {

                if (data === "success") {
                    $('#table').load(document.URL + ' #table', function () {
                        paginate();
                        $(".global-error-inner")
                                .append("<div class='alert alert-success alert-dismissible' role='alert'>  <button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times;</span></button>Succesfully retired assignment!</div>");
                    });
                } else {
                    $(".global-error-inner")
                            .append("<div class='alert alert-danger alert-dismissible' role='alert'>  <button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times;</span></button>" + data + "</div>");
                }
                $('body').find('#retireAssignmentModal')
                        .modal('hide');
            });
});

