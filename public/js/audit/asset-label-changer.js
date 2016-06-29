$(document).ready(function () {
    labels(parseInt($(this).find(":selected").attr('data-group')));

    $("#assetGroups").change(function () {
        labels(parseInt($(this).find(":selected").attr('data-group')));
    });

    function labels(group) {
        if (group === 2) {
            $('#fore-label').html("Forename");
            $('#middle-label').html("Middle Name");
            $('#sur-label').html("Surname");
        } else if(group === 1) {
            $('#fore-label').html("Model");
            $('#middle-label').html("Type");
            $('#sur-label').html("Brand");
        } else {
            $('#fore-label').html("Name");
            $('#middle-label').html("Owner");
            $('#sur-label').html("Company");
        }
    }
});