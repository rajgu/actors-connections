<html>
  <head>
    <title>Bootstrap 101 Template</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" rel="stylesheet" media="screen">
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
    <script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1/jquery-ui.min.js"></script>
    <link href="<?php echo base_url('public/jquery.ui.theme.css');?>" rel="stylesheet" media="screen">

<style>
.ui-autocomplete {
position: absolute;
top: 100%;
left: 0;
z-index: 1000;
float: left;
display: none;
min-width: 160px;
_width: 160px;
padding: 4px 0;
margin: 2px 0 0 0;
list-style: none;
background-color: #ffffff;
border-color: #ccc;
border-color: rgba(0, 0, 0, 0.2);
border-style: solid;
border-width: 1px;
-webkit-border-radius: 5px;
-moz-border-radius: 5px;
border-radius: 5px;
-webkit-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
-moz-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
-webkit-background-clip: padding-box;
-moz-background-clip: padding;
background-clip: padding-box;
*border-right-width: 2px;
*border-bottom-width: 2px;
 
.ui-menu-item > a.ui-corner-all {
display: block;
padding: 3px 15px;
clear: both;
font-weight: normal;
line-height: 18px;
color: #555555;
white-space: nowrap;
 
&.ui-state-hover, &.ui-state-active {
color: #ffffff;
text-decoration: none;
background-color: #0088cc;
border-radius: 0px;
-webkit-border-radius: 0px;
-moz-border-radius: 0px;
background-image: none;
}
}
}
</style>




  </head>
  <body>





  <script type="text/javascript">
  $(function() {
    function log( message ) {
      $( "<div>" ).text( message ).prependTo( "#log" );
      $( "#log" ).scrollTop( 0 );
    }
 
    $( "#actor1" ).autocomplete({
      source: function( request, response ) {
        $.ajax({
          type: 'POST',
          url: "<?php  print (base_url() . "ajax/search") ?>",
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
          url: "<?php  print (base_url() . "ajax/search") ?>",
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
        $('#newThread').on('click', function(e){
            e.preventDefault(); // preventing default click action
            $.ajax({
                url: 'ajax/validate',
                type: 'post',
                data: {
                  actor1: $('#actor1').val(),
                  actor2: $('#actor2').val(),
                },
                success: function(data){
                    // ajax callback
                    console.log(data);
                }, error: function(){
                    alert('ajax failed');
                },
            })
        })
    })



  </script>



























  <div class="container">
  <br><br><br>
    <div class="row">
      <div class="col-lg-8">

        <div class="input-group">
          <span class="input-group-addon">Actor #1 </span>
          <input type="text" class="form-control" placeholder="Enter Name" id="actor1">
        </div>
<Br>
        <div class="input-group">
          <span class="input-group-addon">Actor #2 </span>
          <input type="text" class="form-control" placeholder="Enter Name" id="actor2">
        </div>

<br>
            <button id = "newThread" class="btn btn-primary">CHECK CONNECTION NOW</button>

      </div>
    </div>
  </div>

</body>
</html>