/**
 * main.aac.js
 */

jQuery(function($) {
    // masks
    $('.phone').mask('(999) 999-9999');
    $('.id_number').mask('999.999.999-99');
    $('.code').mask('AAA-AAA');
    $('.date').mask('99/99/9999');
    
    $('#search_id').click(function() {
        var id_number = $('#cad_id_number').val();
        id_number = id_number.replace(/\D/g,'');

        // console.log(id_number);
        if(id_number !== '') {
            $('#results').html('loading...');
            $.ajax({
                type: 'POST',
                url: $('#form_url').val(),
                data: 'id_number=' + id_number,
                dataType: 'html',
                cache: false,
                success:function(data) {
                    $('#results').html(data);
                    $('.idBox').remove();
                    //$('#cad_id_number').attr("disabled", true);
                    //$('#cad_id_number').val('');
                }
            });
        }
    }); 

    $('#cad_id_number').keypress(function(e) {
        var key = e.which;
        if(key === 13) {
            $('#search_id').click();
            return false;
        }
    });
});