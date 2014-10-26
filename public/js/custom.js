function scrollToAnchor(aid){
    var aTag = $("div[id='"+ aid +"']");
    $('html,body').animate({scrollTop: aTag.offset().top},'slow');
}

$("#logo").click(function() { scrollToAnchor('main'); });
$("#m_main").click(function() { scrollToAnchor('main'); });
$("#m_stats").click(function() { scrollToAnchor('stats'); });
$("#m_about").click(function() { scrollToAnchor('about'); });
$("#m_contact").click(function() { scrollToAnchor('contact'); });
$("#f_main").click(function() { scrollToAnchor('main'); });
$("#f_stats").click(function() { scrollToAnchor('stats'); });
$("#f_about").click(function() { scrollToAnchor('about'); });
$("#f_contact").click(function() { scrollToAnchor('contact'); });
$("#link_1").click(function() { scrollToAnchor('footer'); });
$("#link_2").click(function() { scrollToAnchor('footer'); });
var IsRequest = false;;


$(function() {
    function log( message ) {
        $( "<div>" ).text( message ).prependTo( "#log" );
        $( "#log" ).scrollTop( 0 );
    }
    $( "#actor1" ).autocomplete({
        source: function( request, response ) {
            $.ajax({
                type: 'POST',
                url: 'ajax/search',
                data: {
                    search: $('#actor1').val(),
                },
                success: function( data ) {
                    response( data );
                }
            });
        },
        minLength: 3,
        messages: {
            noResults: '',
            results: function() {}
        },
        select: function( event, ui ) {
            log( ui.item ?
            "Selected: " + ui.item.label :
            "Nothing selected, input was " + this.value);
        },
        open: function() {
            $( this ).removeClass( "ui-corner-all" ).addClass( "ui-corner-top" );
        },
        close: function() {
            $( this ).removeClass( "ui-corner-top" ).addClass( "ui-corner-all" );
        }
    });
});


$(function() {
    function log( message ) {
        $( "<div>" ).text( message ).prependTo( "#log" );
        $( "#log" ).scrollTop( 0 );
    }
 
    $( "#actor2" ).autocomplete({
        source: function( request, response ) {
            $.ajax({
                type: 'POST',
                url: 'ajax/search',
                data: {
                    search: $('#actor2').val(),
                },
                success: function( data ) {
                    response( data );
                }
            });
        },
        minLength: 3,
        messages: {
            noResults: '',
            results: function() {}
        },
        select: function( event, ui ) {
            log( ui.item ?
            "Selected: " + ui.item.label :
            "Nothing selected, input was " + this.value);
        },
        open: function() {
            $( this ).removeClass( "ui-corner-all" ).addClass( "ui-corner-top" );
        },
        close: function() {
            $( this ).removeClass( "ui-corner-top" ).addClass( "ui-corner-all" );
        }
    });
});

$(function(){
    $('#Search').on('click', function(e){
        if (IsRequest)
            return;
        show_wait ();
        e.preventDefault(); // preventing default click action
        $.ajax({
            url: 'ajax/validate',
            type: 'post',
            data: {
                actor1: $('#actor1').val(),
                actor2: $('#actor2').val(),
            },
            timeout: 15000,
            success: function(data) {
                hide_wait ();
                var types = [];
                types['warning'] = BootstrapDialog.TYPE_WARNING;
                types['fail']    = BootstrapDialog.TYPE_INFO;
                types['error']   = BootstrapDialog.TYPE_DANGER;
                if (typeof(data.status) == 'undefined') {
                    var data = [];
                    data.status  = 'error';
                    data.text    = 'Congratulation u broke me...';
                }
                if (data.status == 'ok') {
                    var content = '<div class="text-center">';
                    var i = 0;
                    data.text.forEach (function (pos) {
                        i++;
                        link = 'http://www.imdb.com/find?q=' + encodeURIComponent (pos.data);
                        if (pos.type == 'actor') {
                            content += '<div><a target="_imdb" href="' + link + '" class="btn btn-primary"><span class="glyphicon glyphicon-user"></span>&nbsp;&nbsp;' + pos.data + '</a></div>';
                            if (data.text.length != i)
                                content += '<p>played&nbsp;in...</p>';
                        } else {
                            content += '<div><a target="_imdb" href="' + link + '" class="btn btn-primary"><span class="glyphicon glyphicon-film"></span>&nbsp;&nbsp;' + pos.data + '</a></div>';
                            content += '<p>with...</p>';
                        }
                    });
                    content += '</div>';
                    BootstrapDialog.show({
                        type: BootstrapDialog.TYPE_INFO,
                        title: "Hurray, I've got It...",
                        message: content
                    });
                } else {
                    BootstrapDialog.show({
                        type: types[data.status],
                        title: "Ehmmm...",
                        message: data.text
                    });
                }
            }, error: function(){
                hide_wait ();
                BootstrapDialog.show({
                    type: BootstrapDialog.TYPE_DANGER,
                    title: "Ehmmm...",
                    message: 'Congratulation u broke me...'
                });
            },
            })
        })
})


$(function(){
    $('#submit').on('click', function(e){
        e.preventDefault(); // preventing default click action
        $.ajax({
            url: 'ajax/contact',
            type: 'post',
            data: {
                name: $('#name').val(),
                email: $('#email').val(),
                message: $('#message').val(),
                captcha: $('#captcha').val(),
            },
            success: function(data) {
                $('#captcha').val ('');
                $('#captcha-img').html (data.captcha);
                if (data.status == 'ok') {
                    BootstrapDialog.show({
                        type: BootstrapDialog.TYPE_INFO,
                        title: "Everythink seems fine :)",
                        message: data.text
                    });
                } else {
                    BootstrapDialog.show({
                        type: BootstrapDialog.TYPE_WARNING,
                        title: "Something wrong...",
                        message: data.text.join ('<br />'),
                    });
                }
            },
            error: function() {
            }
        })
    })
})


function show_wait () {
    IsRequest = true;
    $('#Search').html ('<img id="spinner" src="public/imgs/spinner.gif" class="spinner" />' + $('#Search').html());
}


function hide_wait () {
    IsRequest = false;
    $('#spinner').remove();
}
