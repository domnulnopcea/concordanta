$('document').ready(function () {
    $("[id^='search_delete_word_form_']").click(function (event) {
        event.preventDefault();

        if ($(this).html() == '[Sterge]') {
            var word_form_id = $(this).attr("id").replace('search_delete_word_form_', '');
            var word_form_name = $('#search_word_form_name_' + word_form_id).text();

            var options = {
                modal:true,
                draggable:true,
                width:"auto",
                stack:true,
                position:'center',
                autoOpen:false,
                closeOnEscape:true,
                resizable:false,
                title:'Propune pentru stergere forma cuvantului',
                buttons:[
                    {
                        text:"Sterge",
                        click:function () {
                            $.ajax({
                                type:"POST",
                                url:'./ajax/ajax_propose_delete_word_form.php',
                                data:{
                                    id:word_form_id
                                },
                                success:function (response) {
                                    $('#search_delete_word_form_' + word_form_id).replaceWith('<span>[Propus pentru stergere]</span>');
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
            $("#stergeForma").html("Sunteti sigur ca doriti sa propuneti pentru stergere forma derivata:  " + word_form_name + '?');
            $("#stergeForma").dialog(options);
            $("#stergeForma").dialog('open');
        }
    });
    // Create tabs in the index.php file.
    var tabs = $('#mainTabs').tabs();
    // Added the ability to sort the tabs.
    tabs.find( ".ui-tabs-nav" ).sortable({
      axis: "x",
      stop: function() {
        tabs.tabs( "refresh" );
      }
    });
});