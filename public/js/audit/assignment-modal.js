        $("#audits").select2({
            tags: true,
            placeholder: "Select audits.."
        });
        $('body').on('click', '#saveAssignments', function (e) {

            $selectData = $('body').find('#audits').val();
            $asset = $('body').find('#assetID').val();
            var formData = {
                "Audits": $selectData,
                "Asset": $asset
            };


            $.ajax({
                type: 'POST',
                url: "//doug.portal.solutionhost.co.uk/apps2/public/Audit/Assignment/setAssignments",
                data: formData,
                dataType: 'json',
                encode: true
            })
                    .then(function () {
                        $('#assignmentModal').modal('hide');
                        $('#table').load(document.URL + ' #table', function () {

                            paginate();

                        });
                    });
        });

        $('#assignmentModal').on('show.bs.modal', function (event) {
            
            var modal = $(this);
            var button = $(event.relatedTarget); // Button that triggered the modal
            var assetName = button.attr('data-assetName'); // Extract info from data-* attributes
            var assetId = button.attr('data-asset'); // Extract info from data-* attributes
            $("#audits").empty();
            modal.find('#leadText i').empty().remove();
            modal.find('#inUse li').empty().remove();
            $.ajax({
                type: 'GET',
                url: "//doug.portal.solutionhost.co.uk/apps2/public/Audit/Assignment/getAuditsNotAssigned/" + assetId,
                dataType: 'json',
                encode: true
            })
                    .done(function (data) {
                       
                        if (data.data.notIn.length === 0) {
                            $('body').find('#saveAssignments').hide();
                            var newOption = new Option("All audits have been assigned", null, true, true);
                            $("#audits").append(newOption).trigger('change');
                            $("#audits").prop("disabled", true);
                        } else {
                            $('body').find('#saveAssignments').show();
                            $.each(data.data.notIn, function (i, item) {
                                var newOption = new Option(item.Name, item.Value, false, false);
                                $("#audits").append(newOption).trigger('change');
                                $("#audits").prop("disabled", false);
                            });
                        }


                        if (data.data.inUse.length === 0) {
                            modal.find('#inUse').append("<li class='list-group-item list-group-item-warning'>Currently nothing assigned.</li>");
                        } else {
                            $.each(data.data.inUse, function (i, item) {
                                modal.find('#inUse').append("<li class='list-group-item list-group-item-info'>" + item.name + "</li>");
                            });
                        }

                    });
            modal.find('.modal-title').text('Assign audits to ' + assetName);
            modal.find('#leadText').append("<i>" + assetName + "</i>");
            modal.find('#assetID').val(assetId);
        });

        function paginate() {
            $('body').find('#table.paginated').each(function () {
                var currentPage = 0;
                var numPerPage = 5;
                var $table = $('body').find('#table');
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
                $('body').find('#pagi').empty();
                $('body').find('#pagi').append($pager).find('.page-number:first').addClass('active');
            });
        }
        ;
