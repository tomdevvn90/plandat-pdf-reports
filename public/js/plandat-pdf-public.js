(function ($) {
    "use strict";

    //1. Template Login
    $(window).load(function(){
      $('#user_login').attr('placeholder','example@emailaddress.com');
      $('#user_pass').attr('placeholder','......');
    });


    //2. Report page
    $( document ).ready(function() {

      $('.btn-share-users').magnificPopup({
          type: 'inline',
          midClick: true,
          mainClass: 'mfp-fade'
      });

      $('.form-submit-share-user').submit(function(){
        var email_user = $(this).find('input[name="email_user"]').val();
        var report_id  = $(this).find('input[name="report_id"]').val();
        var form_pp    = $('#form-share-pdf-user');
        var error_msg  = $('.error-msg');
        var $this = this;
        $.ajax({
            type : "POST",
            dataType : "json",
            url : ajax_object.ajax_url,
            data : {
                action     : "share_report_user",
                email_user : email_user,
                report_id  : report_id
            },
            beforeSend: function(){
              form_pp.addClass('ajax-loading');
            },
            success: function(response) {
              error_msg.empty();
              if(response.status == 'error'){
                error_msg.html(response.error);
              }else{
                $($this).find('input[name="email_user"]').val('');
                error_msg.html('Success!');
              }
              form_pp.removeClass('ajax-loading');
            },
            error: function( jqXHR, textStatus, errorThrown ){
                //Làm gì đó khi có lỗi xảy ra
                console.log( 'The following error occured: ' + textStatus, errorThrown );
            }
        });
        return false;
      });

      var currentTab = 0; // Current tab is set to be the first tab (0)
      showTab(currentTab); // Display the current tab

      $('#nextBtn').on('click',function(e){
        e.preventDefault();
        nextPrev(1);
      });

      $('#prevBtn,.pd-back-step').on('click',function(e){
        e.preventDefault();
        nextPrev(-1);
      });

      $('.item-project').on('click',function(){
        $('.item-project').removeClass('active');
        $(this).addClass('active');
      });

      $('.item-state').on('click',function(){
        $('.item-state').removeClass('active');
        $('.item-state').addClass('un-active');
        $(this).removeClass('un-active');
        $(this).addClass('active');
      });

      $('input[name="pd_project"]').on('click',function(){
        $('#nextBtn').click();
      });

      $('input[name="pd_state"]').on('click',function(){

        if(ajax_object.link_order_report){
          var name = $(this).data('name');
          if(name == 'New South Wales'){
            $('#nextBtn').click();
          }else{
            window.location.href = ajax_object.link_order_report + '?state=' + name;
          }
        }else{
          $('#nextBtn').click();
        }

      });

      $('input[name="pd_btn_confirm"],input[name="pd_btn_confirm_1"]').on('change',function(){
        var val = $(this).val();
        if(val == 0){
          $('.continue-btn').prop('disabled', true);
          $('.continue-btn').addClass('un-click');
        }else{
          $('.continue-btn').prop('disabled', false);
          $('.continue-btn').removeClass('un-click');
        }
      });

      var total_cost = 0;
      $('input[name="pd_order_confirm[]"]').on('change',function(){
          if($(this).is(':checked')){
            total_cost = parseFloat(total_cost) + parseFloat($(this).data('price'));
          }else{
            total_cost = parseFloat(total_cost) - parseFloat($(this).data('price'));
          }
          if(ajax_object.discount && total_cost > 0){
            var $total_new = total_cost - ((total_cost*ajax_object.discount / 100));
            $('.total-cost').html('<span class="sale">'+formatCurrency(total_cost)+'</span> ' + formatCurrency($total_new));
          }else{
            $('.total-cost').html(formatCurrency(total_cost));
          }
          $('input[name="total_price"]').val(total_cost);
          if(total_cost > 0){
            $('#pd-form-actions').show();
          }else{
            $('#pd-form-actions').hide();
          }
      });

      function formatCurrency(total) {
          var neg = false;
          if(total < 0) {
              neg = true;
              total = Math.abs(total);
          }
          return (neg ? "-$" : '$') + parseFloat(total, 10).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, "$1,").toString();
      }

      function showTab(n) {
        // This function will display the specified tab of the form...
        var x = document.getElementsByClassName("tab");
        var actions = document.getElementById("pd-form-actions");

        if(!x[n]) return false;

        x[n].style.display = "block";
        //... and fix the Previous/Next buttons:

        if( n == 1 || n == 2){
          actions.style.display = 'none';
        }else{
          actions.style.display = 'block';
        }

        var confirm = $('#pd_btn_confirm_1').prop('checked');
        if(n == 4){
          confirm = $('#pd_btn_confirm_3').prop('checked');
        }

        if((n == 3 || n == 4) && !confirm){
          $('.continue-btn').prop('disabled', true);
          $('.continue-btn').addClass('un-click');
        }else{
          $('.continue-btn').prop('disabled', false);
          $('.continue-btn').removeClass('un-click');
        }

        if (n == (x.length - 1)) {
          actions.style.display = 'none';
          document.getElementById("nextBtn").innerHTML = "Finish &#8594;";
        } else {
          document.getElementById("nextBtn").innerHTML = "Continue &#8594;";
        }

      }

      function nextPrev(n) {
        // This function will figure out which tab to display
        var x = document.getElementsByClassName("tab");
        var actions = document.getElementById("pd-form-actions");
        // Exit the function if any field in the current tab is invalid:
        if (n == 1 && !validateForm(n)) return false;
        // Hide the current tab:
        x[currentTab].style.display = "none";
        // Increase or decrease the current tab by 1:
        currentTab = currentTab + n;
        // if you have reached the end of the form...
        if (currentTab >= x.length) {
          // ... the form gets submitted:
          document.getElementById("regForm").submit();
          actions.style.display = "none";
          return false;
        }
        // Otherwise, display the correct tab:
        showTab(currentTab);
      }

      function validateForm() {
        // This function deals with validation of the form fields
        var x, y, i, valid = true;
        x = document.getElementsByClassName("tab");
        y = x[currentTab].getElementsByTagName("input");
        // A loop that checks every input field in the current tab:
        for (i = 0; i < y.length; i++) {
          // If a field is empty...
          if (y[i].value == "") {
            // add an "invalid" class to the field:
            y[i].className += " invalid";
            // and set the current valid status to false
            valid = false;
          }
        }
        return valid; // return the valid status
      }

      function number_format(number, decimals, dec_point, thousands_sep) {
          number = number.toFixed(decimals);

          var nstr = number.toString();
          nstr += '';
          x = nstr.split('.');
          x1 = x[0];
          x2 = x.length > 1 ? dec_point + x[1] : '';
          var rgx = /(\d+)(\d{3})/;

          while (rgx.test(x1))
              x1 = x1.replace(rgx, '$1' + thousands_sep + '$2');

          return x1 + x2;
      }

      //Funtion log search
      function plandat_log_user_search($key){
          var user_id = $('input[name="user_id"]').val();
          jQuery.ajax( {
            method : 'POST',
            url: ajax_object.ajax_url,
            dataType: "json",
            data : {
              'action': 'send_mail_log_user_search',
              'key_search': $key,
              'user_id' : user_id
            },
            success: function( res ) {
              console.log('log:',res);
            },
            error : function (){
              console.log('error!');
            }
          });
      }

        //Search
        if($( "#pac-input" ).length > 0){
          $( "#pac-input" ).autocomplete({
            minLength: 1,
            source: function( request, response ) {
                  jQuery.ajax( {
                    url: "https://api.apps1.nsw.gov.au/planning/viewersf/V1/ePlanningApi/address?noOfRecords=10",
                    dataType: "json",
                    data: {
                      a: request.term
                    },
                    success: function( data ) {
                      var addresses = [];
                      if(!data.length){
                          console.log(request.term);
                          addresses = [
                              {
                                  address: 'Sorry we have had an issue loading the data for your report. <br> We have sent your details to our team to investigate and then contact you.',
                                  value: request.term,
                                  noresult : 1
                              }
                          ];
                          plandat_log_user_search(request.term);
                          response(addresses);
                      }
                      else{
                        data.forEach(function (arrayItem) {
                          addresses.push(arrayItem);
                        });
                      }
                      response( addresses );
                    }
                  } );
            },
            focus: function( event, ui ) {
              // $( "#pac-input" ).val( ui.item.address );
              // return false;
            },
            select: function( event, ui ) {

              //check result
              if(ui.item.noresult) return false;

              //console.log(ui.item);
              var type = $('input[name="pd_project"]:checked').data('slug');
              var prod_id = ui.item.propId;
              var zones = [];
              var build_temp = $('.search-build-report');
              var iframe_url = ajax_object.home_url + '?action=preview_report';
              $( "#pac-input" ).val( ui.item.address );
              $( "input[name='i-size']" ).val( '' );
              $( ".__map" ).empty();
              $( "input[name='property_id']" ).val(prod_id);
              var d = new Date();
              var reportDate =  (d.getMonth()+1) + "/" + d.getDate() + '/' + d.getFullYear();
              var sub_text_type = '';

              if(type == 'Deck')    sub_text_type = 'PlanDat Deck Report';
              if(type == 'Carport') sub_text_type = 'PlanDat Carport Report';
              if(type == 'House')   sub_text_type = 'PlanDat House Report';
              if(type == 'Pergola') sub_text_type = 'PlanDat Pergola Report';
              if(type == 'Pool')    sub_text_type = 'PlanDat Pool Report';
              if(type == 'Shed')    sub_text_type = 'PlanDat Class 10a Shed Report';

              iframe_url = iframe_url + '&address=' + encodeURIComponent(ui.item.address);
              iframe_url = iframe_url + '&type=' + encodeURIComponent(type);
              iframe_url = iframe_url + '&date=' + encodeURIComponent(reportDate);
              iframe_url = iframe_url + '&sub_text=' + encodeURIComponent(sub_text_type);

              //Size
              jQuery.ajax( {
                method : 'POST',
                url: ajax_object.ajax_url,
                dataType: "json",
                data : {
                  'action': 'get_property_size',
            			'property_id': prod_id
                },
                beforeSend : function(){
                  build_temp.addClass('ajax-loading');
                },
                success: function( res ) {
                  var google_map_img = 'https://maps.googleapis.com/maps/api/staticmap?center='+encodeURIComponent(ui.item.address)+'&zoom='+res.zoom+'&size=800x500&key=AIzaSyDCL2e7SZaVmEpB1mQ3KbH4JKrnKlw9qk4&maptype=satellite&path=color:yellow|weight:3|' + res.geo;
                  var img = $('<img />',
                         { id: 'img_map_property',
                           src: google_map_img ,
                         }).appendTo($('.__map'));
                  $( "input[name='i-size']" ).val( res.size + ' m²');
                  $( "input[name='google_map_img']" ).val( google_map_img );
                  build_temp.removeClass('ajax-loading');
                  iframe_url = iframe_url + '&size=' + encodeURIComponent(res.size);
                  $('iframe.preview_report').attr('src',iframe_url);
                  $('iframe.preview_report').removeClass('lazyload');
                },
                error : function (){
                  console.log('error!');
                }
              });

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
                  $( "input[name='i-zone']" ).val( zones.join(", ") );
                  iframe_url = iframe_url + '&zone=' + encodeURIComponent(zones.join(", "));
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
                  $( "input[name='i-lot']" ).val( title );
                  iframe_url = iframe_url + '&lot=' + encodeURIComponent(title);
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
                  $( "input[name='i-council']" ).val( council );
                  iframe_url = iframe_url + '&council=' + encodeURIComponent(council);
                }
              } );
              $( "input[name='i-address']" ).val( ui.item.address );
              $(".txt-property").html(ui.item.address);
              $('.form-actions,.item-btn-confirm,.txt-confirm').show();
              return false;
            }
          })
          .autocomplete( "instance" )._renderItem = function( ul, item ) {
            if(item.noresult){
              return $( "<li>" )
                .append( "<div class='noClick'>" + item.address + "</div>" )
                .appendTo( ul );
            }else{
              return $( "<li>" )
                .append( "<div>" + item.address + "</div>" )
                .appendTo( ul );
            }

          };
        }

    });

})(jQuery);
