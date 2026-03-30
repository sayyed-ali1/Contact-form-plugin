jQuery(document).ready(function($){

    $('#myForm').on('submit', function(e){
        e.preventDefault();

        var form = $(this);

        var formData = {
            action: 'save_form_data',
            name: form.find('input[name="name"]').val(),
            email: form.find('input[name="email"]').val(),
            phone: form.find('input[name="phone"]').val(),
            message: form.find('textarea[name="message"]').val()
        };

        $.post(ajax_object.ajax_url, formData, function(response){
          form.find('.result').html("<p style='color:green;'>"+response+"</p>");
        });

    });

});