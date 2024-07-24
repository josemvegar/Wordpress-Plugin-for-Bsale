/**
 * WooIntcomex Admin JS
 *
 * @since 1.0.0
 */

jQuery(document).ready( function () {

  //close popup
  jQuery('.cd-popup').on('click', function(event){
    if( jQuery(event.target).is('.cd-popup-close') || jQuery(event.target).is('.cd-popup') ) {
      event.preventDefault();
      jQuery(this).removeClass('is-visible');
    }
  });
  //close popup when clicking the esc keyboard button
  jQuery(document).keyup(function(event){
      if(event.which=='27'){
        jQuery('.cd-popup').removeClass('is-visible');
      }
  });
 
  jQuery("#ActualizarJson").click(function (e) {
    e.preventDefault();

    jQuery("#ActualizarJson").html('<i class="fa fa-spinner fa-spin" style="font-size:20px"></i>').addClass('disabled');

    jQuery.ajax({
      type:"POST",
      url: ActualizarJson.url,
      data:{
        action: ActualizarJson.action,
        nonce: ActualizarJson.nonce,

      },
      beforeSend: function() {
        jQuery(".loaderBsaleCommerce").show();
        jQuery('.popup-overlay').fadeIn('slow');
      },
      success: function(data){
        jQuery("#ActualizarJson .fa-spin").remove();
        jQuery("#ActualizarJson").html('Actualizar lista de productos');
        jQuery("#ActualizarJson").removeClass('disabled');
        //console.log(data);

        alert('Actualización completada');
        jQuery(".loaderBsaleCommerce").hide();
        jQuery('.popup-overlay').fadeOut('slow');
        //console.log(JsVarBsaleCommerce.pluginsUrl);
        //console.log(data);
        location.reload();
      }
    });
  });


  /*table = jQuery('#myTablebsale').DataTable( {
  "responsive": true,
  "ajax": {
    "url": "/club/wp-admin/admin-ajax.php?action=datatables_endpoint",
    "dataSrc": "data"
  },

  "columns": [
      { "data" : "id" },
      { "data" : "name" },
      { "data" : "description" },
      { "data" : "product_type_id" },
      { "data" : "product_type" }


  ],
  } );*/

  // JOSE VEGA
  table = jQuery('#myTablebsale').DataTable({
    language: {
      url: '//cdn.datatables.net/plug-ins/2.0.5/i18n/es-ES.json',
    },
    "responsive": true,
    "ajax": {
      "url": "/club/wp-admin/admin-ajax.php?action=datatables_endpoint",
      "dataSrc": "data"
    },
    "columns": [
      { "data": "sku" },
      { "data": "name" },
      { "data": "stock" },
      { "data": "price" },
      { "data": "priceIva" }
    ]
  }); 
  
  //console.log("rgetrhget");


  jQuery("#UpdateStockBsaleCommerce").click(function (e) {
    e.preventDefault();

    jQuery("#UpdateStockBsaleCommerce").html('<i class="fa fa-spinner fa-spin" style="font-size:20px"></i>').addClass('disabled');

    jQuery.ajax({
      type:"POST",
      url: ActualizarStockBsaleCommerce.url,
      data:{
        action: ActualizarStockBsaleCommerce.action,
        nonce: ActualizarStockBsaleCommerce.nonce,

      },
      beforeSend: function() {
        jQuery(".loaderBsaleCommerce").show();
        jQuery('.popup-overlay').fadeIn('slow');
      },
      success: function(data){
        jQuery("#UpdateStockBsaleCommerce .fa-spin").remove();
        jQuery("#UpdateStockBsaleCommerce").html('Actualizar lista de productos');
        jQuery("#UpdateStockBsaleCommerce").removeClass('disabled');

        alert('Actualización completada');
        jQuery(".loaderBsaleCommerce").hide();
        jQuery('.popup-overlay').fadeOut('slow');
        //location.reload();
        //console.log(data);

        
      }
    });
  });



  jQuery("#UpdatePrecioBsaleCommerce").click(function (e) {
    e.preventDefault();

    jQuery("#UpdatePrecioBsaleCommerce").html('<i class="fa fa-spinner fa-spin" style="font-size:20px"></i>').addClass('disabled');

    jQuery.ajax({
      type:"POST",
      url: ActualizarPrecioBsale.url,
      data:{
        action: ActualizarPrecioBsale.action,
        nonce: ActualizarPrecioBsale.nonce,

      },
      beforeSend: function() {
        jQuery(".loaderBsaleCommerce").show();
        jQuery('.popup-overlay').fadeIn('slow');
      },
        success: function(data){
        jQuery("#UpdatePrecioBsaleCommerce .fa-spin").remove();
        jQuery("#UpdatePrecioBsaleCommerce").html('Actualizar lista de productos');
        jQuery("#UpdatePrecioBsaleCommerce").removeClass('disabled');
        console.log(data);

        alert('Actualización completada');
        jQuery(".loaderBsaleCommerce").hide();
        jQuery('.popup-overlay').fadeOut('slow');
        //console.log(data);
        //location.reload();
        
      }
    });
  });

  jQuery("#UpdatePrecioIvaBsaleCommerce").click(function (e) {
    e.preventDefault();

    jQuery("#UpdatePrecioIvaBsaleCommerce").html('<i class="fa fa-spinner fa-spin" style="font-size:20px"></i>').addClass('disabled');

    jQuery.ajax({
      type:"POST",
      url: ActualizarPrecioIvaBsale.url,
      data:{
        action: ActualizarPrecioIvaBsale.action,
        nonce: ActualizarPrecioIvaBsale.nonce,

      },
      beforeSend: function() {
        jQuery(".loaderBsaleCommerce").show();
        jQuery('.popup-overlay').fadeIn('slow');
      },
        success: function(data){
        jQuery("#UpdatePrecioBsaleCommerce .fa-spin").remove();
        jQuery("#UpdatePrecioBsaleCommerce").html('Actualizar lista de productos');
        jQuery("#UpdatePrecioBsaleCommerce").removeClass('disabled');
        console.log(data);

        alert('Actualización completada');
        jQuery(".loaderBsaleCommerce").hide();
        jQuery('.popup-overlay').fadeOut('slow');
        //console.log(data);
        //location.reload();
        
      }
    });
  });


});
