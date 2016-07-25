$(function () {
// there's the gallery and the trash
    var $gallery = $("#gallery"),
            $trash = $("#trash");

// let the gallery items be draggable
    $("li", $gallery).draggable({
        cancel: "a.ui-icon", // clicking an icon won't initiate dragging
        revert: "invalid", // when not dropped, the item will revert back to its initial position
        containment: "document",
        helper: "clone",
        cursor: "move"
    });

    $("li", $trash).draggable({
        cancel: "a.ui-icon", // clicking an icon won't initiate dragging
        revert: "invalid", // when not dropped, the item will revert back to its initial position
        containment: "document",
        helper: "clone",
        cursor: "move"
    });

// let the trash be droppable, accepting the gallery items
    $trash.droppable({
        accept: "#gallery > li",
        activeClass: "ui-state-highlight",
        drop: function (event, ui) {
            deleteImage(ui.draggable);
        }
    });

// let the gallery be droppable as well, accepting items from the trash
    $gallery.droppable({
        accept: "#trash li",
        activeClass: "custom-state-active",
        drop: function (event, ui) {
            recycleImage(ui.draggable);
        }
    });

// image deletion function

    function deleteImage($item) {
        $item.fadeOut(function () {
            var $list = $("ul", $trash).length ?
                    $("ul", $trash) :
                    $("<ul class='gallery ui-helper-reset'/>").appendTo($trash);

            $item.find("a.ui-icon-trash").remove();
            $item.append("<span class='pull-right'><a class='ui-icon-trash lead ' href='#'><i class='fa fa-ban remove-role' style='color:red' aria-hidden='true'> </i></a></span>")
            $item.append().appendTo($list).fadeIn(function () {

            });
        });
    }
// remove from current

    function removeFromCurrent($item) {
        $item.fadeOut(function () {
            var $list = $($gallery).length ?
                    $($gallery) :
                    $("<ul class='trash ui-helper-reset'/>").appendTo($gallery);

            $item.find("a.ui-icon-trash").remove();
            $item.append("<span class='pull-right'><a class='ui-icon-trash lead ' href='#'><i class='fa fa-ban remove-role' style='color:red' aria-hidden='true'> </i></a></span>")
            
            $item.append().appendTo($list).fadeIn(function () {

            });
        });
    }

// resolve the icons behavior with event delegation
    $("ul.gallery > li").click(function (event) {
        var $item = $(this),
                $target = $(event.target);
        if ($target.is("i.fa.fa-arrow-circle-o-right")) {
            deleteImage($item);
        }
        return false;
    });
// resolve the icons behavior with event delegation
    $("ul.trash > li").click(function (event) {
        var $item = $(this),        
                $target = $(event.target);
        if ($target.is("i.fa.fa-ban")) {
            removeFromCurrent($item);
        }
        return false;
    });
});