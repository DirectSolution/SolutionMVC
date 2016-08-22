$('#retireWS').on('show.bs.modal', function (event) {

            var modal = $(this);
            var button = $(event.relatedTarget); // Button that triggered the modal
            var wsName = button.attr('data-ws-name'); // Extract info from data-* attributes
            var wsId = button.attr('data-ws-id'); // Extract info from data-* attributes
            $("#audits").empty();

            modal.find('#retireNow').val(wsId);
            modal.find('.modal-title').text('Retire ' + wsName + "?");
            modal.find('#leadText').append("<i>Are you sure you would like to retire " + wsName + "?</i>");
            modal.find('#wsID').val(wsId);
        });

        $('body').on('click', '#retireNow', function (e) {

            $.ajax({
                type: 'POST',
                url: "//portal.solutionhost.co.uk/apps2/public/Ffc/Adminworksector/retire",
                dataType: 'json',
                data: {
                    "id": $(this).val()
                },
                encode: true
            })
                    .done(function (data) {
                        console.log(data);
                        if (data.status === "success") {
                            $('#table').load(document.URL + ' #table', function () {
                                $(".global-error-inner")
                                        .append("<div class='alert alert-success alert-dismissible' role='alert'>  <button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times;</span></button>" + data.message + "</div>");
                            });
                        } else {
                            $(".global-error-inner")
                                    .append("<div class='alert alert-danger alert-dismissible' role='alert'>  <button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times;</span></button>" + data.message + "</div>");
                        }
                        $('body').find('#retireWS')
                                .modal('hide');
                    });
        });