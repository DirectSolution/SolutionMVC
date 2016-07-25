        $('.sheets').on('click', '.add-section', function (event) {
        var sheetID = $(this).val();
        var sectionCount = $(this).parent().find('.section').length + 1;
        $(this).parent().find(".sections").append("<div class='section'><div class='form-group'><div class='col-xs-10'><label for='methodStatement[sheets][" + sheetID + "][section][" + sectionCount + "][name]'>Section Name</label></div><div class='col-xs-2'><button type='button' class='btn btn-xs btn-danger pull-right remove-section'><i class='fa fa-trash' aria-hidden='true'></i></button></div><input type='text' class='form-control' name='methodStatement[sheets][" + sheetID + "][section][" + sectionCount + "][name]' placeholder='Section Name'></div><div class='form-group'><label for='methodStatement[sheets][" + sheetID + "][section][" + sectionCount + "][description]'>Body Text</label><textarea class='ckeditor' id='ms" + sheetID + "sect" + sectionCount + "' name='methodStatement[sheets][" + sheetID + "][section][" + sectionCount + "][description]'></textarea></div></div>");
        CKEDITOR.replace("ms" + sheetID + "sect" + sectionCount);
        });
        $('.sheets').on('click', '.remove-section', function (event) {
        $(this).parent().parent().parent().remove();
        });
        $('#methods').on('click', '.add-method', function(event){
        var methodsCount = $(this).parent().find('.method').length + 1;
        $(this).parent().find('.method-sections').append("<article class='method'><div class='form-group'><div class='col-xs-10'><label for='methodStatement[methods][" + methodsCount + "][name]'>Method Title</label></div><div class='col-xs-2'><button type='button' class='btn btn-xs btn-danger pull-right remove-method'><i class='fa fa-trash' aria-hidden='true'></i></button></div><input name='methodStatement[methods][" + methodsCount + "][name]' type='text' class='form-control' placeholder='Method Title'></div><div class='form-group'><label for='methodStatement[methods][" + methodsCount + "][description]'>Method Description</label><textarea class='ckeditor' id='methods" + methodsCount + "' name='methodStatement[methods][" + methodsCount + "][description]'></textarea></div></article>");
        CKEDITOR.replace("methods" + methodsCount);
        });
        $('#methods').on('click', '.remove-method', function(event){
        $(this).parent().parent().parent().remove();
        });