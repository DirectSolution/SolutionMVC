$(document).on('click', '.setDefault', function (event) {
    var id = $(this).val();

    $.ajax({
        type: 'POST',
        url: "http://doug.portal.solutionhost.co.uk/apps2/public/Audit/Settings/setDefault",
        data: {
            id: id
        },
        dataType: 'json',
        encode: true,
        async: false
    })
            .then(function (data) {
                $(".alert-success").remove();
//                $('#table').children().remove().fadeOut(300, function() { $(this).remove(); });
                $('#table').load(document.URL + ' #table', function () {
                    pagi();

                });
                $(".global-error-inner").append("<div class='alert alert-success alert-dismissible' role='alert'>  <button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times;</span></button>" + data.message + "</div>");

            });

    event.preventDefault;

    function pagi() {
        $(document).find('#table').each(function () {
//                    alert("HERE");
            var currentPage = 0;
            var numPerPage = 5;
            var $table = $(this);
            $table.bind('repaginate', function () {
                $table.find('tbody tr').hide().slice(currentPage * numPerPage, (currentPage + 1) * numPerPage).show();
            });
            $table.trigger('repaginate');
            var numRows = $table.find('tbody tr').length;
            var numPages = Math.ceil(numRows / numPerPage);
            var $pager = $('<ul class="pagination pagination-lg"></ul>');
            for (var page = 0; page < numPages; page++) {
                $('<li class="page-number"></li>').html("<a href='#'>" + (page + 1) + "</a>").bind('click', {
                    newPage: page
                }, function (event) {
                    currentPage = event.data['newPage'];
                    $table.trigger('repaginate');
                    $(this).addClass('active').siblings().removeClass('active');
                }).appendTo($pager).addClass('clickable');
            }

            $(document).find('#pagi').append($pager).find('.page-number:first').addClass('active');

        });
    }

});