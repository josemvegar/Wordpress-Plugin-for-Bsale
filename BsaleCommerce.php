<?php
/**
 * Bsale con Bsalecommerce
 *
 * Plugin Name: BsaleCommerce
 * Plugin URI:  https://josecortesia.cl
 * Description: Integración de bsale para Bsalecommerce.
 * Version:     1.0
 * Author:      Jose Cortesia
 * Author URI:  https://www.josecortesia.cl
 * License:     GPLv2 or later
 * License URI: http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * Text Domain: BsaleCommerce
 * Domain Path: /languages/
 *
 */

if(!defined('ABSPATH')){die('-1');}

define( 'MY_PLUGIN_PATH_BSALE', plugin_dir_path( __FILE__ ) );
require_once dirname(__FILE__) . '/includes/Consultas.php';
require_once dirname(__FILE__) . '/includes/Admin.php';
$autoload_path = dirname(__FILE__) . '/vendor/autoload.php';
require_once $autoload_path;


function ActivarBsaleCommerce(){
    require_once dirname(__FILE__) . '/includes/ActivarPlugin.php';
    ActBsaleCommerce::Activate();
}
register_activation_hook(__FILE__, 'ActivarBsaleCommerce');

function DesactivarBsaleCommerce(){
    require_once dirname(__FILE__) . '/includes/DesactivarPlugin.php';
    DesBsaleCommerce::Desactivate();    

}
register_deactivation_hook(__FILE__, 'DesactivarBsaleCommerce');

//FUNCION QUE AGREGA LOS JS Y CSS SOLO EN LA PARTE DEL SHORTCODE
function EncolarCssBsaleCommerce() {
    wp_enqueue_style('BsaleCommerceStyle',plugins_url('public/css/BsaleCommerceStyle.css',__FILE__), array(), '1.0', 'all' );

}
add_action('wp_enqueue_scripts', 'EncolarCssBsaleCommerce');


//FUNCION QUE AGREGA LOS JS Y CSS SOLO EN LA PARTE ADMIN DEL PLUGINS
function EncolarAdminBsaleCommerce() {

    wp_enqueue_style('AdminBsaleCommerce', plugins_url('admin/css/AdminBsaleCommerce.css',__FILE__), array(), '1.0', 'all');
    //wp_enqueue_style('jquery.dataTables.min', plugins_url('admin/vendor/datatables/jquery.dataTables.min.css',__FILE__), array(), '1.11.3', 'all');
    //wp_enqueue_style('responsive.dataTables.min', plugins_url('admin/vendor/datatables/responsive.dataTables.min.css',__FILE__), array(), '2.2.9', 'all');
    //wp_enqueue_style('select.dataTables.min', plugins_url('admin/vendor/datatables/select.dataTables.min.css',__FILE__), array(), '1.3.3', 'all');
    //wp_enqueue_style('buttons.dataTables.min', plugins_url('admin/vendor/datatables/buttons.dataTables.min.css',__FILE__), array(), '2.1.0', 'all');
    wp_enqueue_style('font-awesome.min', plugins_url('admin/vendor/font-awesome/font-awesome.min.css',__FILE__), array(), '4.7.0', 'all');
    wp_enqueue_style('dataTables.dataTables.min', plugins_url('admin/datatable/dataTables.dataTables.min.css',__FILE__), array(), '1.3.3', 'all');
    

    wp_enqueue_script('AdminBsaleCommerce', plugins_url('admin/js/AdminBsaleCommerce.js',__FILE__), array('jquery'), '1.12.4');
    //wp_enqueue_script('jquery.dataTables.min', plugins_url('admin/vendor/datatables/jquery.dataTables.min.js',__FILE__), array('jquery'), '1.11.3');
    //wp_enqueue_script('dataTables.responsive.min', plugins_url('admin/vendor/datatables/dataTables.responsive.min.js',__FILE__), array('jquery'), '2.2.9');
    //wp_enqueue_script('dataTables.select.min', plugins_url('admin/vendor/datatables/dataTables.select.min.js',__FILE__), array('jquery'), '1.3.3');
    //wp_enqueue_script('dataTables.buttons.min', plugins_url('admin/vendor/datatables/dataTables.buttons.min.js',__FILE__), array('jquery'), '2.1.0');
    wp_enqueue_script('pdfmake.min.js', plugins_url('admin/vendor/datatables/pdfmake.min.js',__FILE__), array('jquery'), '1.0');
    wp_enqueue_script('vfs_fonts', plugins_url('admin/vendor/datatables/vfs_fonts.js',__FILE__), array('jquery'), '1.0');
    wp_enqueue_script('jszip.min', plugins_url('admin/vendor/datatables/jszip.min.js',__FILE__), array('jquery'), '1.0');
    wp_enqueue_script('buttons.print.min', plugins_url('admin/vendor/datatables/buttons.print.min.js',__FILE__), array('jquery'), '1.0');
    wp_enqueue_script('buttons.html5.min', plugins_url('admin/vendor/datatables/buttons.html5.min.js',__FILE__), array('jquery'), '1.0');
    wp_enqueue_script('dataTables.min', plugins_url('admin/datatable/dataTables.min.js',__FILE__), array('jquery'));


    $plugins_url =  array( 'pluginsUrl' =>MY_PLUGIN_PATH_BSALE ); //after wp_enqueue_script

    wp_localize_script( 'AdminBsaleCommerce', 'JsVarBsaleCommerce', $plugins_url );

    wp_localize_script('AdminBsaleCommerce','ActualizarJson', 

        array(
            'url'    => admin_url( 'admin-ajax.php' ),
            'nonce'  => wp_create_nonce( 'seg' ),
            'action' => 'peticionguardar'
        )
    );

    wp_localize_script('AdminBsaleCommerce','ActualizarStockBsaleCommerce', 

        array(
            'url'    => admin_url( 'admin-ajax.php' ),
            'nonce'  => wp_create_nonce( 'seg' ),
            'action' => 'updatebsale'
        )
    );



    wp_localize_script('AdminBsaleCommerce','ActualizarPrecioBsale', 

        array(
            'url'    => admin_url( 'admin-ajax.php' ),
            'nonce'  => wp_create_nonce( 'seg' ),
            'action' => 'updatepreciobsale'
        )
    );

    wp_localize_script('AdminBsaleCommerce','ActualizarPrecioIvaBsale', 

        array(
            'url'    => admin_url( 'admin-ajax.php' ),
            'nonce'  => wp_create_nonce( 'seg' ),
            'action' => 'updateprecioivabsale'
        )
    );




}
add_action( 'admin_enqueue_scripts', 'EncolarAdminBsaleCommerce' );



/////ACTIVAR LA FUNCION CADA CIERTO TIEMPO
//CREAR EL CRON CUANDO EL PLUGINS SE ACTIVE

//AGREGA A LOS EVENTOS DEL CRON QUE SE EJECUTE CADA 60 MINUTOS

/*function CrontIntervalBsale( $schedules ) {
    $schedules['every60minute'] = array(
            'interval'  => 3600, // time in seconds
            'display'   => 'Every 60 Minute'
    );
    return $schedules;
}
add_filter( 'cron_schedules', 'CrontIntervalBsale' );


function RunOnActivate(){

    if( !wp_next_scheduled( 'CronActualizarStockBsaleCommerce' ) ) {
        wp_schedule_event( time(), 'every60minute', 'CronActualizarStockBsaleCommerce' );
    }
    if( !wp_next_scheduled( 'CronActualizarPricekBsaleCommerce' ) ) {
        wp_schedule_event( time(), 'every60minute', 'CronActualizarPricekBsaleCommerce' );
    }

}
register_activation_hook( __FILE__, 'RunOnActivate' );

//DESACTIVA EL CRON CUANDO EL PLUGINS SE DESACTIVA

function RunOnDeactivate() {
    wp_clear_scheduled_hook('CronActualizarStockBsale');
    wp_clear_scheduled_hook('CronActualizarPriceBsale');
}
register_deactivation_hook( __FILE__, 'RunOnDeactivate' );*/

function CrontIntervalBsale($schedules) {
    $schedules['every60minute'] = array(
            'interval'  => 3600, // tiempo en segundos
            'display'   => 'Cada 60 minutos'
    );
    return $schedules;
}
add_filter('cron_schedules', 'CrontIntervalBsale');

function RunOnActivate(){
    if (!wp_next_scheduled('ActualizarPrecioIvaBsale')) {
        wp_schedule_event(time(), 'every60minute', 'ActualizarPrecioIvaBsale', array($nonce));
    }
    if (!wp_next_scheduled('ActualizarStockBsale')) {
        wp_schedule_event(time(), 'every60minute', 'ActualizarStockBsale', array($nonce));
    }
}
register_activation_hook(__FILE__, 'RunOnActivate');

function RunOnDeactivate() {
    wp_clear_scheduled_hook('ActualizarPrecioIvaBsale');
    wp_clear_scheduled_hook('ActualizarStockBsale');
}
register_deactivation_hook(__FILE__, 'RunOnDeactivate');



function ActualizarPrecioBsaleCreateNonce(){
    $_POST['nonce'] = wp_create_nonce( 'seg' );
    $nonce = $_POST['nonce'];
    ActualizarPrecioBsale($nonce);
}
add_action ('CronActualizarPriceBsale', 'ActualizarPrecioBsaleCreateNonce', 10, 0);

function ActualizarStockBsaleCreateNonce(){
    $_POST['nonce'] = wp_create_nonce( 'seg' );
    $nonce = $_POST['nonce'];
    ActualizarStockBsale($nonce);
}
add_action ('CronActualizarStockBsale', 'ActualizarStockBsaleCreateNonce', 10, 0);

////FUNCION PARA CONSEGUIR EL ID DEL PRODUCTO POR MEDIO DEL SKU
function GetIDProductBySku($Sku){

    global $wpdb;
    $product_id = $wpdb->get_var(
      $wpdb->prepare("SELECT post_id FROM $wpdb->postmeta WHERE meta_key='_sku' AND meta_value='%s' ORDER BY meta_id DESC LIMIT 1",
        $Sku)
    );
    return $product_id;
}


/*Actualizar JSON*/
function ActualizarJsonBsaleCreate($nonce){
    $nonce = sanitize_text_field( $_POST['nonce'] );

    if (!wp_verify_nonce($nonce, 'seg')) {
        die ("Ajaaaa, estas de noob!");
    }

    function getProductsBsale(){
        $Cla = new CurlRequestBsale();
        $response = $Cla->buildProductsJson();
        return $response;
    }

    $productsBsale   = json_decode(getProductsBsale());
    //var_dump($productsBsale);
    
    $wooArray           = [];

/*foreach ($productsBsale as $ValueproductsBsale) {

   
         var_dump($ValueproductsBsale->variants[0]->code);

}*/


 //var_dump($wooArray);

    $bonito= json_encode($productsBsale, JSON_PRETTY_PRINT);
    $jsondatabsaleproduct = '{"data":'.$bonito.'}';
    file_put_contents(MY_PLUGIN_PATH_BSALE.'/admin/dataBsaleCreate/DataBsale.json', $jsondatabsaleproduct);

/*
    $jsonintcomex = json_encode(array('data' => $wooArray));
    file_put_contents(MY_PLUGIN_PATH.'/admin/dataIntcomex/dataIntComex.json', $jsonintcomex);
*/


}


// JOSE VEGA
add_action('init', 'mi_registrar_hooks');
// Función que registra los hooks de WordPress
function mi_registrar_hooks() {
    // Registra la acción AJAX para manejar la petición 'peticionguardar'
    add_action('wp_ajax_peticionguardar', 'ActualizarJsonBsaleCreate');
}
//-------------------------------------------------------------------------------
//add_action( 'wp_ajax_peticionguardar', 'ActualizarJsonBsaleCreate' );




/*Actualizar STOCK*/
function ActualizarStockBsale($nonce){
//Casa Matriz
    $nonce = sanitize_text_field( $_POST['nonce'] );

    if (!wp_verify_nonce($nonce, 'seg')) {
        die ("Ajaaaa, estas de noob!");
    }

    $Cla = new CurlRequestBsale;

    function getProductsStocksBsale(){
        $Cla = new CurlRequestBsale();
        $response = $Cla->getProductsStocks();
        return $response;
    }

    $productsStockBsale   = getProductsStocksBsale();

    //$arregloStock = json_decode($productsStockBsale);

    foreach ($productsStockBsale as $stocks) {
            
            $sku = $stocks["sku"];
            $stock = ( $stocks["stock"] == 0 || $stocks["stock"] == null ) ? 0 : $stocks["stock"];
            $product_id = GetSkuByIDBsale($sku);

            if ($product_id != null){
                $stockActual = $stock == 0 ? 'outofstock' : 'instock';

                // 1. Updating the stock quantity
                update_post_meta($product_id, '_stock', $stock);

                // 2. Updating the stock status
                update_post_meta( $product_id, '_stock_status', wc_clean( $stockActual ) );

                // 3. Updating post term relationship
                wp_set_post_terms( $product_id, array($stockActual), 'product_visibility', true );
                // 3. Updating post term relationship
                // Get an instance of the product variation from a defined ID
                //$child_product = wc_get_product($product_id);
                // Change the product visibility
                //$child_product->set_catalog_visibility('visible');
                // Save and sync the product visibility
                //$child_product->save();
                // And finally (optionally if needed)
                wc_delete_product_transients( $product_id ); // Clear/refresh the variation cache

            }
            

    
    }



}
add_action( 'wp_ajax_updatebsale', 'ActualizarStockBsale' );

function GetSkuByIDBsale($sku) {
    global $wpdb;

    // Buscar el ID del producto en la tabla postmeta donde el meta_key es '_sku' y el meta_value coincide con el SKU proporcionado
    $product_id = $wpdb->get_var(
        $wpdb->prepare(
            "SELECT post_id FROM $wpdb->postmeta WHERE meta_key = '_sku' AND meta_value = %s",
            $sku
        )
    );

    return $product_id;
}


/*Actualizar PRECIO*/
function ActualizarPrecioBsale($nonce){

    $nonce = sanitize_text_field( $_POST['nonce'] );
    if (!wp_verify_nonce( $nonce , 'seg')) {
        die ("Ajaaaa, estas de noob!");
    }

    $jsonUrl= dirname(__FILE__) . '/admin/dataBsaleCreate/DataBsale.json';
    $json_data = file_get_contents($jsonUrl);
    $data = json_decode($json_data, true);

    $Cla = new CurlRequestBsale;

    function getProductsPriceBsale(){
        $Cla = new CurlRequestBsale();
        $response = $Cla->getPriceLists();
        return $response;
    }

    $productsPriceBsale   = getProductsPriceBsale();

    foreach ($productsPriceBsale as $prices) {

        $sku = $prices["sku"];
        $price = $prices["price"];
        $priceVenta = $prices["priceIva"];
        if(getPriceBySku($data, $sku)== 0 || getPriceBySku($data, $sku) == null){
            $price = 0;
            $priceVenta = 0;
        }
        $product_id = GetSkuByIDBsale($sku);
        
        if (!is_null($product_id)) {
            /*update_post_meta($product_id, '_regular_price', $price );
            update_post_meta($product_id, '_price', $price );
            update_post_meta($product_id, '_visibility', 'visible');
            // 3. Updating post term relationship
            wp_set_post_terms( $product_id, $terms, 'product_visibility', true );
            // Get an instance of the product variation from a defined ID
            //$child_product = wc_get_product($product_id);
            // Change the product visibility
            //$child_product->set_catalog_visibility('visible');
            // Save and sync the product visibility
            //$child_product->save();
            // And finally (optionally if needed)
            wc_delete_product_transients( $product_id ); // Clear/refresh the variation cache*/

            $current_status = get_post_status($product_id);

            // Determinar el nuevo estado del producto
            $new_status = ($price == 0) ? 'draft' : 'publish';

            // Si el estado actual es diferente al nuevo estado, actualizar el estado del producto
            if ($current_status !== $new_status) {
                wp_update_post(array(
                    'ID' => $product_id,
                    'post_status' => $new_status
                ));
            }
            
            // Actualizar los precios

            if($price != 0 ){
                update_post_meta($product_id, '_regular_price', $price);
                update_post_meta($product_id, '_price', $price);
            }
            
            // Actualizar la relación de términos de publicación
            wp_set_post_terms($product_id, $terms, 'product_visibility', true);
            
            // Limpiar/actualizar la caché de variación del producto
            wc_delete_product_transients($product_id);
        
        }
    }
}

function getPriceBySku($data, $sku) {
    foreach ($data['data'] as $product) {
        if ($product['sku'] === $sku) {
            return $product['price'];
        }
    }
    // Si no se encuentra el SKU, devolver un valor predeterminado o null
    return null;
}

add_action( 'wp_ajax_updatepreciobsale', 'ActualizarPrecioBsale' );

/*Actualizar PRECIO IVA*/
function ActualizarPrecioIvaBsale($nonce){

    $nonce = sanitize_text_field( $_POST['nonce'] );
    if (!wp_verify_nonce( $nonce , 'seg')) {
        die ("Ajaaaa, estas de noob!");
    }

    $jsonUrl= dirname(__FILE__) . '/admin/dataBsaleCreate/DataBsale.json';
    $json_data = file_get_contents($jsonUrl);
    $data = json_decode($json_data, true);

    $Cla = new CurlRequestBsale;

    function getProductsPriceBsale(){
        $Cla = new CurlRequestBsale();
        $response = $Cla->getPriceLists();
        return $response;
    }

    $productsPriceBsale   = getProductsPriceBsale();

    foreach ($productsPriceBsale as $prices) {

        $sku = $prices["sku"];
        $price = $prices["price"];
        $priceVenta = $prices["priceIva"];
        if(getPriceBySku($data, $sku)== 0 || getPriceBySku($data, $sku) == null){
            $price = 0;
            $priceVenta = 0;
        }
        $product_id = GetSkuByIDBsale($sku);
        
        if (!is_null($product_id)) {
            /*update_post_meta($product_id, '_regular_price', $price );
            update_post_meta($product_id, '_price', $price );
            update_post_meta($product_id, '_visibility', 'visible');
            // 3. Updating post term relationship
            wp_set_post_terms( $product_id, $terms, 'product_visibility', true );
            // Get an instance of the product variation from a defined ID
            //$child_product = wc_get_product($product_id);
            // Change the product visibility
            //$child_product->set_catalog_visibility('visible');
            // Save and sync the product visibility
            //$child_product->save();
            // And finally (optionally if needed)
            wc_delete_product_transients( $product_id ); // Clear/refresh the variation cache*/

            $current_status = get_post_status($product_id);

            // Determinar el nuevo estado del producto
            $new_status = ($priceVenta == 0) ? 'draft' : 'publish';

            // Si el estado actual es diferente al nuevo estado, actualizar el estado del producto
            if ($current_status !== $new_status) {
                wp_update_post(array(
                    'ID' => $product_id,
                    'post_status' => $new_status
                ));
            }
            
            // Actualizar los precios

            if($price != 0 ){
                update_post_meta($product_id, '_regular_price', $priceVenta);
                update_post_meta($product_id, '_price', $priceVenta);
            }
            
            // Actualizar la relación de términos de publicación
            wp_set_post_terms($product_id, $terms, 'product_visibility', true);
            
            // Limpiar/actualizar la caché de variación del producto
            wc_delete_product_transients($product_id);
        
        }
    }
}

add_action( 'wp_ajax_updateprecioivabsale', 'ActualizarPrecioIvaBsale' );





/*Generar Factura*/
function PutOrderBsaleCreate($order_id){

$order = wc_get_order( $order_id );
$id_order = $order->get_order_number();
$statusOrden = $order->get_status();
$orderdate = strtotime($order->get_date_created());
$correoBoleta = $order->get_billing_email();
$productoOrdenArray = [];

    foreach ( $order->get_items() as $item_id => $item ) {

        $product = $item->get_product();
        $quantity = $item->get_quantity();
        $item_sku = $product->get_sku();
        $precioConIVA = $item->get_total();
        $precioSinIVA =  $precioConIVA - ($precioConIVA * 0.19);
        $product_name = $item->get_name();
        $precioProductoSinIVA = $product->get_price() / 1.19;


        $productoOrdenArray[] = [
            'netUnitValue' => $precioProductoSinIVA,
            'quantity' => $quantity,
            'comment' => $product_name,
            'taxes' => array( array(

                 'code' => 14,
                 'percentage' =>  19

            )),
        ];

        
    }
        error_log(print_r($productoOrdenArray,true));


    $postdata = '
        {
          "codeSii": 39,
          "officeId": "4",
          "priceListId": 3,
          "emissionDate": '.$orderdate.',
          "details": '.json_encode($productoOrdenArray).'
        } 
    ';

        $Cla = new CurlRequestBsale();
        $responseBoleta3 = $Cla->generarBoleta($postdata);


       $responseBoleta2 = '{"href":"https://api.bsale.cl/v1/documents/57426.json","id":57426,"emissionDate":1670976000,"expirationDate":1670976000,"generationDate":1670998346,"number":55367,"serialNumber":null,"trackingNumber":null,"totalAmount":473780.0,"netAmount":398134.0,"taxAmount":75646.0,"exemptAmount":0.0,"exportTotalAmount":0.0,"exportNetAmount":0.0,"exportTaxAmount":0.0,"exportExemptAmount":0.0,"commissionRate":0.0,"commissionNetAmount":0.0,"commissionTaxAmount":0.0,"commissionTotalAmount":0.0,"percentageTaxWithheld":0.0,"purchaseTaxAmount":0.0,"purchaseTotalAmount":0.0,"address":"","municipality":"","city":"","urlTimbre":null,"urlPublicView":"https://app2.bsale.cl/view/27264/c24f1c2ea561?sfd=99","urlPdf":"https://app2.bsale.cl/view/27264/c24f1c2ea561.pdf?sfd=99","urlPublicViewOriginal":"https://app2.bsale.cl/view/27264/c24f1c2ea561","urlPdfOriginal":"https://app2.bsale.cl/view/27264/c24f1c2ea561.pdf","token":"c24f1c2ea561","state":0,"commercialState":0,"urlXml":"https://api.bsale.cl/v1/27264/files/c24f1c2ea561.xml","ted":null,"salesId":null,"informedSii":2,"responseMsgSii":null,"document_type":{"href":"https://api.bsale.cl/v1/document_types/1.json","id":"1"},"office":{"href":"https://api.bsale.cl/v1/offices/4.json","id":"4"},"user":{"href":"https://api.bsale.cl/v1/users/4.json","id":"4"},"coin":{"href":"https://api.bsale.cl/v1/coins/1.json","id":"1"},"references":{"href":"https://api.bsale.cl/v1/documents/57426/references.json"},"document_taxes":{"href":"https://api.bsale.cl/v1/documents/57426/document_taxes.json"},"details":{"href":"https://api.bsale.cl/v1/documents/57426/details.json"},"sellers":{"href":"https://api.bsale.cl/v1/documents/57426/sellers.json"},"attributes":{"href":"https://api.bsale.cl/v1/documents/57426/attributes.json"}}';


        $boleta = json_decode($responseBoleta3);


        $notas1 = 'Su boleta ha sido generado con satisfactoriamente, puede descargarla desde aqui '.print_r($boleta->urlPdf, TRUE);
        $order->add_order_note($notas1);

        error_log(print_r($boleta->urlPdf,true));

        $headers = 'From: Equipo de Soporte <contacto@josecortesia.cl>' . "\r\n";
        wp_mail($correoBoleta, 'ENVIO DE BOLETA SIMPLE BSALE', $notas1, $headers );




/*



{
  "codeSii": 39,
  "officeId": "4",
  "priceListId": 4,
  "emissionDate": 1670682205,
  "details": [
    {
      "netUnitValue": 10916,
      "quantity": 1,
      "comment": "ACEITE DE OLIVA CORATINA",
      "taxes": [
        {
          "code": 14,
          "percentage": 19
        }
      ]
    },
    {
      "netUnitValue": 10916,
      "quantity": 1,
      "comment": "Galletas Saca Pita Romero 200 Grs",
      "taxes": [
        {
          "code": 14,
          "percentage": 19
        }
      ]
    }
  ]
} 



*/



}

/*Probar Orden*/
/*
woocommerce_order_status_pending
woocommerce_order_status_failed
woocommerce_order_status_on-hold
woocommerce_order_status_processing
woocommerce_order_status_completed
woocommerce_order_status_refunded
woocommerce_order_status_cancelled
*/
//add_action( 'woocommerce_payment_complete_order_status_processing', 'PutOrderBsale' );
//add_action( 'woocommerce_payment_complete_order_status_completed', 'PutOrderBsale' );
//add_action( 'woocommerce_checkout_order_processed', 'PutOrderBsale', 10, 3 );
//add_action('woocommerce_order_status_changed','PutOrderBsale');
add_action('woocommerce_order_status_completed','PutOrderBsaleCreate', 10, 1);


function AjaxEndPointBsale(){
        echo file_get_contents(MY_PLUGIN_PATH_BSALE.'admin/dataBsaleCreate/DataBsale.json');
        wp_die();

}
add_action('wp_ajax_datatables_endpoint', 'AjaxEndPointBsale'); //logged in
add_action('wp_ajax_no_priv_datatables_endpoint', 'AjaxEndPointBsale'); //not logged in