$(document).ready(function () {
    $.ajax({
        type: 'POST',
        url: '//doug.portal.solutionhost.co.uk/apps2/public/portal/newsfeed/getNews',
        dataType: 'json',
        encode: true
    })
            .done(function (data) {
                var i = 0;
                var feeder = setInterval(getfeed, 4000);
                function getfeed() {
                    i++;
                    $("#newsFeedUL").append("<li class='item'><h3><span style='background-color:" + data[i].bg + ";' class='ticker-badge'> " + data[i].feed + " </span> <a href='" + data[i].link + "'> " + data[i].title + "</a></h3><p>" + data[i].description + " - <time>" + data[i].pubDate + "</time></p></li>");
                    $("li.item").prev().remove();
                    if (i >= data.length)
                        i = 0;
                }
                $("#newsTickerBox").mouseenter(function () {
                    clearInterval(feeder);
                });
                $("#newsTickerBox").mouseleave(function () {
                    feeder = setInterval(getfeed, 4000);
                });
            });
});
