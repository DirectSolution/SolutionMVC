$(document).ready(function () {

    $('body').on('click', '.addQuestion', function (e) {

        e.stopPropagation();
        e.preventDefault();
        cloneQuestion(parseInt($(this).val()));

    });

    $("body").on("click", "button.removeGroup", removeGroup);
    $("body").on("click", "button.removeQuestion", removeQuestion);
    $("body").on("click", "button.addGroup", addGroup);

    var regex = /(\d+)(.+?)(\d+)$/i;
    function cloneQuestion(vi) {
        alert(vi);
        var cloneIndex1 = $(".question-row-" + vi).find(".clonedInput").length;
        var a = $("body").find(".question-row-" + vi);
               
        var aplus = a.find('.groupIndexHidden').val();
        alert(aplus);
        var aaplus = (aplus);
        var cloneIndex2 = cloneIndex1 + 1;
        $(".clonedInput:last").clone()
                .appendTo(".question-row-" + vi)
                .attr("class", "row clonedInput clonedInput" + cloneIndex2)
                .find("*")
                .each(function () {
                    var id = this.id || "";
                    var match = this.id.match(regex) || [];
                    if (match.length) {
                        this.name = "groups[" + (aaplus) + "][questions][" + cloneIndex1 + "][" + match[0].replace(/\d+/g, '') + "]";
                        this.id = vi + match[0].replace(/\d+/g, '') + (cloneIndex2);
                    }
                });
//                .on('click', 'button.addQuestion', cloneQuestion)
//                .on('click', 'button.removeGroup', removeGroup)
//                .on('click', 'button.removeQuestion', removeQuestion);

        $(".question-row-" + vi + " .clonedInput" + cloneIndex2).find('.textarea').val('');
        $(".question-row-" + vi + " .clonedInput" + cloneIndex2).find('.add-expiry').attr('for', vi + 'add_expiry' + cloneIndex2).val(1).attr('checked', true);
        $(".question-row-" + vi + " .clonedInput" + cloneIndex2).find('.expiry-required').attr('for', vi + 'expiry_required' + cloneIndex2).attr('checked', false);
        $(".question-row-" + vi + " .clonedInput" + cloneIndex2).find('.add-evidence').attr('for', vi + 'add_evidence' + cloneIndex2).attr('checked', false);
        $(".question-row-" + vi + " .clonedInput" + cloneIndex2).find('.evidence-required').attr('for', vi + 'evidence_required' + cloneIndex2).attr('checked', false);

    }
    function removeQuestion(id) {
        if ($(this).parent().parent().parent().parent().children().length > 1) {
            $(this).parent().parent().parent(".clonedInput").remove();
        }
    }

    function removeGroup() {
//        if ($(this).parent().parent().parent().parent().children().length > 1) {
//alert("HERE");
        $(this).parent().parent().parent().parent(".question-group").remove();
//        }
    }
    ;
    function addGroup() {
//        alert("HER");
        var groupIndex = $(".question-group").length + 1;
        var a = $(document).find('.question-group:last');
        var aplus = a.find('.groupIndexHidden').val();
        var aaplus = ++aplus;
        var vi = groupIndex;
        var cloneIndex1 = $(".question-row-" + vi).find(".clonedInput").length;
        var cloneIndex2 = cloneIndex1 + 1;

        var template = $('#hidden-template').html();
        var item = $(template).clone();
//        alert(++aplus);
        $(item).find('.groupIndexHidden').val(aaplus);
        $(item).find("*")
                .each(function () {
                    var id = this.id || "";
                    var match = this.id.match(regex) || [];
                    if (match.length) {
                        this.name = "groups[" + (aaplus) + "][questions][" + cloneIndex1 + "][" + match[0].replace(/\d+/g, '') + "]";
                        this.id = groupIndex + match[0].replace(/\d+/g, '') + (cloneIndex2);
                    }
                });

        $(item).find('.add-expiry').attr('for', aaplus + 'add_expiry' + cloneIndex2);
        $(item).find('.expiry-required').attr('for', aaplus + 'expiry_required' + cloneIndex2);
        $(item).find('.add-evidence').attr('for', aaplus + 'add_evidence' + cloneIndex2);
        $(item).find('.evidence-required').attr('for', aaplus + 'evidence_required' + cloneIndex2);
        $(item).find('.question-row').attr('class', 'panel-body question-row-' + aaplus);
        $(item).find('.addQuestion').val(aaplus);
        $(item).find('.group_name').attr('name', "groups[" + (--aaplus) + "][name]");
        $(item).find('.group_question').attr('name', "groups[" + (--aaplus) + "][questions][0][question]");
        $(item).find('.group_question_type').attr('name', "groups[" + (--aaplus) + "][questions][0][QuestionTypes_id]");
        $('.page-container').append(item);
//                .on('click', 'button.addQuestion', cloneQuestion);
//                .on('click', 'button.removeGroup', removeGroup)
//                .on('click', 'button.removeQuestion', removeQuestion);
    }
});