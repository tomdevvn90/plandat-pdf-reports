<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
  <title>PlanDat</title>

  <h1>PlanDat</h1>
  <style>
  .ui-autocomplete-loading {
    background: white url("/wp-content/themes/Divi-child/ui-anim_basic_16x16.gif") right center no-repeat;
  }
  </style>
  <script type='text/javascript' src='/wp-includes/js/jquery/jquery.min.js?ver=3.6.4' id='jquery-core-js'></script>
  <link rel="stylesheet" href="/wp-content/themes/Divi-child/jquery-ui.min.css">
  <script src="/wp-content/themes/Divi-child/jquery-ui.min.js"></script>
  <script>
    jQuery( function() {

      jQuery( "#birds" ).autocomplete({
        source: function( request, response ) {
          jQuery.ajax( {
            url: "https://api.apps1.nsw.gov.au/planning/viewersf/V1/ePlanningApi/address?noOfRecords=10",
            dataType: "json",
            data: {
              a: request.term
            },
						beforeSend: function(){
							jQuery( "#button" ).hide();
							jQuery('#birds').addClass('ui-autocomplete-loading');
						},
            success: function( data ) {
              // {"address":"30 301-303 ANZAC PARADE KINGSFORD 2032","propId":1975721,"GURASID":73275935}
							var addresses = [];
							data.forEach(function (arrayItem) {
								addresses.push(arrayItem);
							});
							response( addresses );
            }
          } );
        },
        minLength: 2,
        select: function( event, ui ) {

					var prod_id = ui.item.propId;
					jQuery('#birds').addClass('ui-autocomplete-loading');
					//Size
					jQuery.ajax( {
						method : 'POST',
						url: '<?php echo admin_url( 'admin-ajax.php' ) ?>',
						dataType: "json",
						data : {
							'action': 'get_property_size',
							'property_id': prod_id
						},
						success: function( res ) {
							jQuery( "input[name='i-size']" ).val( res.size + ' m²' );
							jQuery('#birds').removeClass('ui-autocomplete-loading');
							jQuery( "#button" ).show();
						},
						error : function (){
							console.log('error!');
						}
					});
					var zones = [];
					//Zone
					jQuery.ajax( {
						url: "https://api.apps1.nsw.gov.au/planning/viewersf/V1/ePlanningApi/layerintersect?type=property&id="+prod_id+"&layers=epi",
						dataType: "json",
						success: function( data ) {
							data.forEach(function (arrayItem) {
								if(arrayItem.id == 19){
									arrayItem.results.forEach(function (zone) {
										zones.push(zone.title);
									});
								}
							});
							jQuery( "input[name='i-zone']" ).val( zones.join(", ") );
						}
					} );

					//Lot
					jQuery.ajax( {
						url: "https://api.apps1.nsw.gov.au/planning/viewersf/V1/ePlanningApi/lot?propId="+prod_id,
						dataType: "json",
						success: function( data ) {
							var title = '';
							data.forEach(function (arrayItem) {
								if(title == ''){
									title = arrayItem.attributes.LotDescription;
								}
							});
							jQuery( "input[name='i-lot']" ).val( title );
						}
					} );

					//Council
					jQuery.ajax( {
						url: "https://api.apps1.nsw.gov.au/planning/viewersf/V1/ePlanningApi/council?propId="+prod_id,
						dataType: "json",
						success: function( data ) {
							var council = '';
							data.forEach(function (arrayItem) {
								if(council == ''){
									council = arrayItem;
								}
							});
							jQuery( "input[name='i-council']" ).val( council );
						}
					} );
					jQuery('input[name="propId"]').val(prod_id);
					jQuery('input[name="address"]').val(ui.item.address);
					return false;
        }
      }).autocomplete( "instance" )._renderItem = function( ul, item ) {
				return jQuery( "<li>" )
					.append( "<div>" + item.address + "</div>" )
					.appendTo( ul );
			};
    } );
  </script>
</head>
<body>
  <?php
  $type_report = isset($_POST['type_report']) ? $_POST['type_report'] : '';
  $address = isset($_POST['address']) ? $_POST['address'] : '';
   ?>
  <form method="POST" action="">
    <div class="ui-widget">
      <label for="birds">Type: </label>
      <select class="" name="type_report">
        <?php
        $types = get_terms( array(
          'taxonomy'   => 'type-reports',
          'hide_empty' => false,
        ) );
        foreach ($types as $type) {
          $thumbnail = get_field('image_featured', 'type-reports_' . $type->term_id);
          $selected = ($type_report == $type->name) ? 'selected="selected"' : '';
          ?>
          <option value="<?php echo $type->name; ?>" <?php echo $selected; ?>><?php echo $type->name; ?></option>
          <?php
        }
        ?>
      </select>
    </div>
    <br>
    <div class="ui-widget">
      <label for="birds">Address: </label>
			<input id="birds" name="address" value="<?php echo $address; ?>">
			<input type="hidden" name="propId" value="">
      <input type="hidden" name="i-size" value="">
			<input type="hidden" name="i-zone" value="">
			<input type="hidden" name="i-council" value="">
			<input type="hidden" name="i-lot" value="">
			<input type="submit" name="test_report" value="GO" id="button" style="display:none;" />
    </div>
  </form>
