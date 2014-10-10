$('document').ready(function () {
    $("[id^='delete_word_form_']").click(function (event) {
        event.preventDefault();

        if ($(this).html() == '[x]') {
            var dont_ask_dialog = $('#dont_ask_delete_derivate_form_flag').val();

            var word_form_id = $(this).attr("id").replace('delete_word_form_', '');
            var word_form_name = $('#word_form_name_' + word_form_id).text();

            var options = {
                modal:true,
                draggable:true,
                width:"auto",
                stack:true,
                position:'center',
                autoOpen:false,
                closeOnEscape:true,
                resizable:false,
                title:'Sterge forma cuvantului',
                buttons:[
                    {
                        text:"Sterge",
                        click:function () {
                            $.ajax({
                                type:"POST",
                                url:'./ajax/ajax_delete_word_form.php',
                                data:{
                                    id:word_form_id
                                },
                                success:function (response) {
                                    $('#delete_word_form_' + word_form_id).replaceWith('<span>[Sters]</span>');
                                    $("#stergeForma").dialog('destroy');
                                    $("#stergeForma").empty();
                                }
                            });
                        }
                    },
                    {
                        text:"Cancel",
                        click:function () {
                            $(this).dialog("close");
                        }
                    }
                ],
                close:function () {
                    $("#stergeForma").dialog('destroy');
                    $("#stergeForma").empty();
                }
            };

            if (dont_ask_dialog == 0) {
                $("#stergeForma").html("Sunteti sigur ca doriti sa stergeti forma derivata:  " + word_form_name + '?');
                $("#stergeForma").dialog(options);
                $("#stergeForma").dialog('open');
            } else {
                // just delete
                $.ajax({
                    type:"POST",
                    url:'./ajax/ajax_delete_word_form.php',
                    data:{
                        id:word_form_id
                    },
                    success:function (response) {
                        $('#delete_word_form_' + word_form_id).replaceWith('<span>[Sters]</span>');
                    }
                });
            }
        }
    });

    $("[id^='for_hp_']").click(function (event) {
        event.preventDefault();

        if ($(this).html() == 'For Homepage') {

            var word_id = $(this).attr("id").replace('for_hp_', '');

            var options = {
                modal:true,
                draggable:true,
                width:"auto",
                stack:true,
                position:'center',
                autoOpen:false,
                closeOnEscape:true,
                resizable:false,
                title:'Adauga cuvantul pentru prima pagina',
                buttons:[
                    {
                        text:"Adauga",
                        click:function () {
                            $.ajax({
                                type:"POST",
                                url:'./ajax/ajax_set_for_homepage.php',
                                data:{
                                    id:word_id
                                },
                                success:function (response) {
                                    $('#for_hp_' + word_id).replaceWith('<span>Setat pentru Homepage</span>');
                                    $("#forHomepage").dialog('destroy');
                                    $("#forHomepage").empty();
                                }
                            });
                        }
                    },
                    {
                        text:"Cancel",
                        click:function () {
                            $(this).dialog("close");
                        }
                    }
                ],
                close:function () {
                    $("#forHomepage").dialog('destroy');
                    $("#forHomepage").empty();
                }
            };
            $("#forHomepage").html("Sunteti sigur ca doriti sa adaugati cuvantul pentru prima pagina?");
            $("#forHomepage").dialog(options);
            $("#forHomepage").dialog('open');
        }
    });


    $("[id^='confirm_suggestion_']").live('click', function (event) {
        event.preventDefault();

        var suggestion_ids = $(this).attr("id").replace('confirm_suggestion_', '');
        var suggestion_ids_arr = suggestion_ids.split('_');
        var suggestion_id = suggestion_ids_arr[0];
        var word_form_id = suggestion_ids_arr[1];
        $.ajax({
            type:"POST",
            url:'./ajax/ajax_confirm_suggestion.php',
            data:{
                id:suggestion_id,
                word_form_id:word_form_id
            },
            success:function (response) {
                $('#confirm_suggestion_' + suggestion_id + '_' + word_form_id).replaceWith('<input id="confirm_suggestion_' + suggestion_id + '_' + word_form_id + '" type="button" disabled="disabled" value="Stergere confirmata" />');
                $('#cancel_suggestion_' + suggestion_id + '_' + word_form_id).replaceWith('<input id="cancel_suggestion_' + suggestion_id + '_' + word_form_id + '" type="button" value="Anuleaza" />');
            }
        });
    });

    $("[id^='cancel_suggestion_']").live('click', function (event) {
        event.preventDefault();

        var suggestion_ids = $(this).attr("id").replace('cancel_suggestion_', '');
        var suggestion_ids_arr = suggestion_ids.split('_');
        var suggestion_id = suggestion_ids_arr[0];
        var word_form_id = suggestion_ids_arr[1];

        $.ajax({
            type:"POST",
            url:'./ajax/ajax_cancel_suggestion.php',
            data:{
                id:suggestion_id,
                word_form_id:word_form_id
            },
            success:function (response) {
                $('#cancel_suggestion_' + suggestion_id + '_' + word_form_id).replaceWith('<input type="button" disabled="disabled" id="cancel_suggestion_' + suggestion_id + '_' + word_form_id + '" value="Sugestie anulata" />');
                $('#confirm_suggestion_' + suggestion_id + '_' + word_form_id).replaceWith('<input type="button" value="Confirma stergere" id="confirm_suggestion_' + suggestion_id + '_' + word_form_id + '" />');
            }
        });
    });

    $("[id^='cancel_delete_word_form_']").live('click', function (event) {
        event.preventDefault();

        var word_form_id = $(this).attr("id").replace('cancel_delete_word_form_', '');
        $.ajax({
            type:"POST",
            url:'./ajax/ajax_cancel_delete_word_form.php',
            data:{
                id: word_form_id
            },
            success:function (response) {
                location.reload();
            }
        });
    });

    $("#table_list tr:not(:first-child)").live('hover',
        function (event) {

            if (event.type == 'mouseover') {
                $(this).css('background-color', '#F0F0F0');
                $(this).css('cursor', 'pointer');
            }
            if (event.type == 'mouseout') {

                $(this).css('background-color', 'white');
            }
        }
    );
})