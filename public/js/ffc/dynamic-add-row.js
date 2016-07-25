$(document).ready(function () {
    $('#question-table').on('click', '.addRow', function (e) {
        $("#init").remove();
        var numItems = $("#questions").find('.question-row').length + 1;

        $("#questions").append(appendString(numItems));
    });

    function appendString(numItems) {
        return "<tr class='question-row' data-row-index='"+numItems+"'><td><textarea class='form-control'  placeholder='Question' name='sector[questions]["+numItems+"][field_name]' required ></textarea></td><td><select name='sector[questions][" + numItems + "][field_type]' class='form-control'><option value='freetext'>Freetext</option><option value='yesnona'>Yes/no/na</option><option value='textbox'>Textarea</option></select></td><td><div class='radio'><label><input type='radio' name='sector[questions][" + numItems + "][field_req]'  value='1' checked>Yes</label></div><div class='radio'><label><input type='radio' name='sector[questions][" + numItems + "][field_req]'  value='0'>No</label></div></td><td><div class='radio'><label><input type='radio' name='sector[questions][" + numItems + "][automatic_unapprove]'  value='1' checked>Yes</label></div><div class='radio'><label><input type='radio' name='sector[questions][" + numItems + "][automatic_unapprove]'  value='0'>No</label></div></td><td><div class='radio'><label><input type='radio' name='sector[questions][" + numItems + "][add_expiry_date]'  value='1' checked>Yes</label></div><div class='radio'><label><input type='radio' name='sector[questions][" + numItems + "][add_expiry_date]'  value='0'>No</label></div></td><td><div class='radio'><label><input type='radio' name='sector[questions][" + numItems + "][expiry_required]' value='1' checked>Yes</label></div><div class='radio'><label><input type='radio' name='sector[questions][" + numItems + "][expiry_required]' value='0'>No</label></div></td><td><div class='radio'><label><input type='radio' name='sector[questions][" + numItems + "][add_evidence]' value='1' checked>Yes</label></div><div class='radio'><label><input type='radio' name='sector[questions][" + numItems + "][add_evidence]' value='0'>No</label></div></td><td><div class='radio'><label><input type='radio' name='sector[questions][" + numItems + "][evidence_required]' value='1' checked>Yes</label></div><div class='radio'><label><input type='radio' name='sector[questions][" + numItems + "][evidence_required]' value='0'>No</label></div></td><td><button type='button' class='btn btn-danger remove-row' value='"+numItems+"'><i class='fa fa-trash' aria-hidden='true'></i></button></td></tr>";
    }
    
    $('#question-table').on('click', '.remove-row', function(){
        console.log("clicked");
        var rowVal = $(this).val();
        
        $('#question-table').find("*[data-row-index='"+rowVal+"']").remove();
        
    });

});