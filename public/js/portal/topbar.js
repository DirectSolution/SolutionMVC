$(".support-button").click(function (e) {
    e.preventDefault;
    window.open('/apps/documentation/help/', 'Help', 'width=1000, height=600');
});
$(".settings-button").click(function (e) {
    e.preventDefault;
    window.open('/apps/custservice/chprofile.php', 'Technical Support', 'width=800, height=600');
});
$(".userguide-button").click(function (e) {
    e.preventDefault;
    window.open('/apps/portal/userguide.html', 'Tutorials', 'width=1000, height=600');
});

$("#client").on('keyup',function (e) {
    var client = $("#client");
    var empty = $.trim($("#client").val());
    if (!client.val().match(/^\d+$/) && empty.length > 0) {
        $(this).val(
                function (index, value) {
                    return value.replace(/\D/g,'');
                });
        alert("This is the client number box, you can only enter numbers in here. Your login username and password go in the other two boxes.");
    }
});
$("#username").on('keyup',function (e) {
    var username = $("#username");
    var empty = $.trim($("#username").val());
    if (!username.val().match(/^[a-zA-Z\d]*$/) && empty.length > 0) {
        $(this).val(
                function (index, value) {
                    return value.replace(/[^a-zA-Z0-9]/g,'');
                });
        alert("This is the username box, you can only enter letters and numbers in here.\n\nYour login username comprises of your first name initial and your surname, for example - John Smith would be jsmith.\n\nSome users may have numbers in their usernames.");
    }
});