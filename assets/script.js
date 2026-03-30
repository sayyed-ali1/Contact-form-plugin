jQuery(document).ready(function($){

    $('#myForm').on('submit', function(e){
        e.preventDefault();

        var form = $(this);
        var btn = form.find('#submitBtn');

        var name = form.find('input[name="name"]').val().trim();
        var email = form.find('input[name="email"]').val().trim();
        var phone = form.find('input[name="phone"]').val().trim();
        var message = form.find('textarea[name="message"]').val().trim();

        var error = "";

        // VALIDATION
        if(name === "") error += "Name is required<br>";
        if(!email.includes("@")) error += "Valid email required<br>";
        if(phone.length < 12) error += "Phone must be 11 digits<br>";
        if(message === "") error += "Message is required<br>";

        if(error !== ""){
           form.find('.result').html("<div style='color:red; background:#ffecec; padding:10px; border-radius:8px;'>"+error+"</div>");
            return;
        }

        // 🔥 LOADER START
        btn.text("Submiting...");
        btn.prop("disabled", true);

        // AJAX
        var formData = {
            action: 'save_form_data',
            name: name,
            email: email,
            phone: phone,
            message: message
        };

        $.post(ajax_object.ajax_url, formData, function(response){

            // SUCCESS
            form.find('.result').html("<div style='color:green;'>"+response+"</div>");

            // RESET
            form[0].reset();

            // 🔥 LOADER STOP
            btn.text("Submit");
            btn.prop("disabled", false);

        });

    });

});