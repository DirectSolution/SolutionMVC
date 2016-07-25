
$(document).ready(function () {
    
    paginate();
    
function paginate() {
    $('body').find('#table.paginated').each(function () {
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
        $('body').find('#pagi').append($pager).find('.page-number:first').addClass('active');
        
        
        
    });
}

});