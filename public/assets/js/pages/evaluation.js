$(function() {

    $('[data-rel=tooltip]').tooltip();

    $(".select2").css('width', '150px').select2({allowClear: true})
            .on('change', function() {
        $(this).closest('form').validate().element($(this));
    });


    var $validation = false;
    $('#fuelux-wizard').ace_wizard().on('change', function(e, info) {
        if (info.step == 1 && $validation) {
            if (!$('#sample-form').valid())
                return false;
        }
    }).on('finished', function(e) {
        bootbox.dialog("Thank you! Your information was successfully saved!", [{
                "label": "OK",
                "class": "btn-small btn-primary",
            }]
                );
    }).on('stepclick', function(e) {
        //return false;//prevent clicking on steps
    });


    
    



    //documentation : http://docs.jquery.com/Plugins/Validation/validate


    $.mask.definitions['~'] = '[+-]';
    $('#phone').mask('(999) 999-9999');

    jQuery.validator.addMethod("phone", function(value, element) {
        return this.optional(element) || /^\(\d{3}\) \d{3}\-\d{4}( x\d{1,6})?$/.test(value);
    }, "Enter a valid phone number.");

    




    $('#modal-wizard .modal-header').ace_wizard();
    $('#modal-wizard .wizard-actions .btn[data-dismiss=modal]').removeAttr('disabled');
})