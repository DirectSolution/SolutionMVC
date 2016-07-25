$('#retireAssetModal').on('show.bs.modal', function (event) {

    var modal = $(this);
    var button = $(event.relatedTarget); // Button that triggered the modal
    var assetName = button.attr('data-assetName'); // Extract info from data-* attributes
    var assetId = button.attr('data-asset'); // Extract info from data-* attributes
    $("#audits").empty();

    modal.find('#retireNow').val(assetId);
    modal.find('.modal-title').text('Retire ' + assetName + "?");
    modal.find('#leadText').append("<i>Are you sure you would like to retire " + assetName + "?</i>");
    modal.find('#assetID').val(assetId);
});

$('body').on('click', '#retireNow', function (e) {

    $.ajax({
        type: 'POST',
        url: "//doug.portal.solutionhost.co.uk/apps2/public/Audit/Asset/retire",
        dataType: 'json',
        data: {
            "asset": $(this).val()
        },
        encode: true
    })
            .done(function (data) {

                if (data === "success") {
                    $('#table').load(document.URL + ' #table', function () {
                        paginate();                        
                        $(".global-error-inner")
                                .append("<div class='alert alert-success alert-dismissible' role='alert'>  <button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times;</span></button>Succesfully retired asset!</div>");
                    });
                } else {
                    $(".global-error-inner")
                            .append("<div class='alert alert-danger alert-dismissible' role='alert'>  <button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times;</span></button>" + data + "</div>");
                }
                $('body').find('#retireAssetModal')
                                .modal('hide');
            });
});