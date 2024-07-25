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
 
  /*jQuery("#ActualizarJson").click(function (e) {
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
  });*/


  jQuery("#ActualizarJson").click(function (e) {
    e.preventDefault();

    jQuery("#ActualizarJson").html('<i class="fa fa-spinner fa-spin" style="font-size:20px"></i>').addClass('disabled');

    function loadProducts(page = 0) {
        jQuery.ajax({
            type: "POST",
            url: ActualizarJson.url,
            data: {
                action: ActualizarJson.action,
                nonce: ActualizarJson.nonce,
                page: page
            },
            beforeSend: function() {
                jQuery(".loaderBsaleCommerce").show();
                jQuery('.popup-overlay').fadeIn('slow');
            },
            success: function(data) {
                if (data.success) {
                    if (data.data.hasMorePages) {
                        sessionStorage.setItem('currentPage', page + 1);
                        loadProducts(page + 1);
                    } else {
                        sessionStorage.removeItem('currentPage');
                        jQuery("#ActualizarJson .fa-spin").remove();
                        jQuery("#ActualizarJson").html('Actualizar lista de productos');
                        jQuery("#ActualizarJson").removeClass('disabled');
                        alert('Actualización completada');
                        jQuery(".loaderBsaleCommerce").hide();
                        jQuery('.popup-overlay').fadeOut('slow');
                        location.reload();
                    }
                } else {
                    console.log(data.data.message);
                    alert('Error: ' + data.data.message);
                    sessionStorage.removeItem('currentPage');
                }
            },
            error: function() {
                // Intentar de nuevo desde la página actual
                var retryPage = parseInt(sessionStorage.getItem('currentPage')) || page;
                loadProducts(retryPage);
            }
        });
    }

    // Iniciar desde la página guardada o desde la página 0
    var currentPage = parseInt(sessionStorage.getItem('currentPage')) || 0;
    loadProducts(currentPage);
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


  /*jQuery("#UpdateStockBsaleCommerce").click(function (e) {
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
        location.reload();

        
      }
    });
  });*/


  jQuery("#UpdateStockBsaleCommerce").click(function (e) {
    e.preventDefault();

    jQuery("#UpdateStockBsaleCommerce").html('<i class="fa fa-spinner fa-spin" style="font-size:20px"></i>').addClass('disabled');

    function loadStock(page = 0) {
        jQuery.ajax({
            type: "POST",
            url: ActualizarStockBsaleCommerce.url,
            data: {
                action: ActualizarStockBsaleCommerce.action,
                nonce: ActualizarStockBsaleCommerce.nonce,
                page: page
            },
            beforeSend: function() {
                jQuery(".loaderBsaleCommerce").show();
                jQuery('.popup-overlay').fadeIn('slow');
            },
            success: function(data) {
                if (data.success) {
                    if (data.data.hasMorePages) {
                        sessionStorage.setItem('currentStockPage', page + 1);
                        loadStock(page + 1);
                    } else {
                        sessionStorage.removeItem('currentStockPage');
                        jQuery("#UpdateStockBsaleCommerce .fa-spin").remove();
                        jQuery("#UpdateStockBsaleCommerce").html('Actualizar lista de productos');
                        jQuery("#UpdateStockBsaleCommerce").removeClass('disabled');
                        alert('Actualización completada');
                        jQuery(".loaderBsaleCommerce").hide();
                        jQuery('.popup-overlay').fadeOut('slow');
                        location.reload();
                    }
                } else {
                    console.log(data.data.message);
                    alert('Error: ' + data.data.message);
                    sessionStorage.removeItem('currentStockPage');
                }
            },
            error: function() {
                var retryPage = parseInt(sessionStorage.getItem('currentStockPage')) || page;
                loadStock(retryPage);
            }
        });
    }

    var currentPage = parseInt(sessionStorage.getItem('currentStockPage')) || 0;
    loadStock(currentPage);
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
