(function($) {
    // Initially hide the form
    $("#data-form").hide();

    // Show the form when the add button is clicked
    $(".add-new-data").on("click", function(event) {
        event.preventDefault();
        $("#data-form").show();
        $('input[name="name"]').val('');
        $('input[name="email"]').val('');
        $('input[name="id"]').val('');
    });

    // Show the form with the data for the given id when the edit button is clicked
    $("body").on("click", ".edit-data", function(event) {
        event.preventDefault();
        $("#data-form").show();

        var url = new URL($(this).attr("href"));
        var id = url.searchParams.get("id");

        $.post(ajax_object.ajax_url, {
            action: "get_data",
            id: id,
            _ajax_nonce: ajax_object.cop_nonce
        }, function(response) {
            console.log('response', response);
            if(response.success && response.data) {
                $('input[name="name"]').val(response.data.name);
                $('input[name="email"]').val(response.data.email);
                $('input[name="id"]').val(response.data.id);
            } else {
                alert(response.data);
            }
        });
    });


    // Create Data
    nonce = ajax_object.nonce;
    $("#data-form").on("submit", function(event) {
        event.preventDefault();
        var name =  $('input[name="name"]').val();
        var email = $('input[name="email"]').val();
        var id = $('input[name="id"]').val();
        if (!name || !email) {
            alert("Name and Email are required");
            return;
        }      
        $.post(ajax_object.ajax_url, {
            action: id ? 'update_data' : 'create_data',
            name: name,
            email: email,
            id: id,
            _ajax_nonce: ajax_object.cop_nonce
        }, function(response) {
            if(response.success) {
                location.reload();
            } else {
                alert(response.data);
            }
        });
    });
})(jQuery);