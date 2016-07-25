$(document).ready(function () {

    $.ajax({
        type: 'GET',
        url: "http://doug.portal.solutionhost.co.uk/apps2/public/Audit/AnswerSets/overUnderDueAudits",
        dataType: 'json',
        encode: true
    })
            .then(function (data) {
                var len = data.over.length;
                if (len > 0) {
                    $.each(data.over, function (i, val) {
                        $(document).find('.badge').text(len);
                        $(document)
                                .find("#overDue")
                                .append("<div class='alert alert-warning off-canvas-message' role='alert'><h4><strong>Warning!</strong></h4><b>'" + val.Asset.Audit.name + "'</b> for <b>'" + val.Asset.Asset.forename + " " + val.Asset.Asset.surname + "'</b> is overdue by <b>" + val.OverdueBy.days + "</b> days and <b>" + val.OverdueBy.h + "</b> hours. <div class='row'><div class='col-xs-12'><a class='btn btn-warning btn-xs pull-right' style='margin-top:8px;' href='//doug.portal.solutionhost.co.uk/apps2/public/Audit/Audit/TakeAudit/" + val.Asset.Audit.id + "/" + val.Asset.Asset.id + "'>Take audit now?</a></div></div></div>");
                    });
                } else {
                    $(document).find('.overdue-container').hide();
                }
            });
});