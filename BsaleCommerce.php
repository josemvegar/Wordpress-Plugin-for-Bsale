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

//PENDIENTE CON ESTO DE ABAJO QUE LO DESACTIVE PARA PROBAR -----------------------------
/*function CrontIntervalBsale($schedules) {
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
register_activation_hook(__FILE__, 'RunOnActivate');*/

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
/*function ActualizarJsonBsaleCreate($nonce){
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
    
    $wooArray           = [];

    $bonito= json_encode($productsBsale, JSON_PRETTY_PRINT);
    $jsondatabsaleproduct = '{"data":'.$bonito.'}';
    file_put_contents(MY_PLUGIN_PATH_BSALE.'/admin/dataBsaleCreate/DataBsale.json', $jsondatabsaleproduct);


}*/


function ActualizarJsonBsaleCreate() {
    $nonce = sanitize_text_field($_POST['nonce']);
    if (!wp_verify_nonce($nonce, 'seg')) {
        die("Ajaaaa, estas de noob!");
    }

    $page = isset($_POST['page']) ? intval($_POST['page']) : 0;
    $productsBsale = get_transient('products_bsale') ?: [];

    try {
        $Cla = new CurlRequestBsale();
        $newProducts = json_decode($Cla->getProducts($page), true);
        $productsBsale = array_merge($productsBsale, $newProducts);

        // Guardar productos combinados en un transitorio
        set_transient('products_bsale', $productsBsale, 3600); // Guardar por 1 hora

        // Verificar si hay más páginas
        $hasMorePages = count($newProducts) > 0;
        if (!$hasMorePages) {
            $bonito = json_encode($productsBsale, JSON_PRETTY_PRINT);
            $jsondatabsaleproduct = '{"data":' . $bonito . '}';
            file_put_contents(MY_PLUGIN_PATH_BSALE . '/admin/dataBsaleCreate/DataBsale.json', $jsondatabsaleproduct);

            // Eliminar el transitorio después de guardar
            delete_transient('products_bsale');
            //$hasMorePages= false;
        }

        wp_send_json_success(['hasMorePages' => $hasMorePages]);

    } catch (Exception $e) {
        wp_send_json_error(['message' => $e->getMessage()]);
    }
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
/*function ActualizarStockBsale($nonce){
//Casa Matriz
    $nonce = sanitize_text_field( $_POST['nonce'] );

    if (!wp_verify_nonce($nonce, 'seg')) {
        die ("Ajaaaa, estas de noob!");
    }

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



}*/


function ActualizarStockBsale($page = 0, $is_cron = false) {
    $nonce = sanitize_text_field($_POST['nonce']);
    if (!wp_verify_nonce($nonce, 'seg') && $is_cron==false) {
        die("Ajaaaa, estas de noob!");
    }
    if ($is_cron==false){
        $page = isset($_POST['page']) ? intval($_POST['page']) : 0;
        //$productsStockBsale = get_transient('products_stock_bsale') ?: [];   
    }

    try {
        $Cla = new CurlRequestBsale();
        $newStocks = json_decode($Cla->getProductsStocks($page), true);
        //$productsStockBsale = array_merge($productsStockBsale, $newStocks);

        //set_transient('products_stock_bsale', $productsStockBsale, 3600); // Guardar por 1 hora

        $hasMorePages = count($newStocks) > 0;
        //if (!$hasMorePages) {
        //foreach ($productsStockBsale as $stocks) {
            foreach ($newStocks as $stocks) {
                $sku = $stocks["sku"];
                $stock = ($stocks["stock"] == 0 || $stocks["stock"] == null) ? 0 : $stocks["stock"];
                $product_id = GetSkuByIDBsale($sku);

                if ($product_id != null) {
                    // Activar la gestión de inventario
                    update_post_meta($product_id, '_manage_stock', 'yes');
                    
                    $stockActual = $stock == 0 ? 'outofstock' : 'instock';
                    update_post_meta($product_id, '_stock', $stock);
                    update_post_meta($product_id, '_stock_status', wc_clean($stockActual));
                    wp_set_post_terms($product_id, array($stockActual), 'product_visibility', true);
                    wc_delete_product_transients($product_id);
                }
            }

            //delete_transient('products_stock_bsale');
        //}
        
        // Si hay más páginas y se está ejecutando desde WP-Cron, programar una nueva ejecución
        if ($hasMorePages && $is_cron) {
            wp_schedule_single_event(time() + 20, 'actualizar_stock_bsale_event', [$page + 1, true]);
        }
        
        if($is_cron==false){
            wp_send_json_success(['hasMorePages' => $hasMorePages]);            
        }


    } catch (Exception $e) {
        wp_send_json_error(['message' => $e->getMessage()]);
    }
}

//add_action('wp_ajax_' . ActualizarStockBsaleCommerce.action, 'ActualizarStockBsale');



add_action( 'wp_ajax_updatebsale', 'ActualizarStockBsale' );


/*CRON JV*/

// Hook para el evento de WP-Cron, ejecuta la función de actualización
add_action('actualizar_stock_bsale_event', 'ActualizarStockBsale', 10, 2);

// Función para registrar la tarea de WP-Cron que se ejecuta cada hora
/*function registrar_actualizacion_stock_bsale_cron() {
    if (!wp_next_scheduled('actualizar_stock_bsale_event')) {
        //wp_schedule_event(time(), 'hourly', 'actualizar_stock_bsale_event', [0, true]);
    }
}
//add_action('wp', 'registrar_actualizacion_stock_bsale_cron');
add_action('init', 'registrar_actualizacion_stock_bsale_cron');
*/

/**
 * Registrar el evento WP-Cron en la activación del plugin
 */
function registrar_actualizacion_stock_bsale_cron() {
    if ( ! wp_next_scheduled( 'actualizar_stock_bsale_event' ) ) {
        wp_schedule_event( time(), 'hourly', 'actualizar_stock_bsale_event', [0, true] );
        error_log('Evento programado.');
    }
}
register_activation_hook( __FILE__, 'registrar_actualizacion_stock_bsale_cron' );

/**
 * Eliminar el evento WP-Cron en la desactivación del plugin
 */
function desactivar_actualizacion_stock_bsale_cron() {
    $timestamp = wp_next_scheduled( 'actualizar_stock_bsale_event' );
    if ( $timestamp ) {
        wp_unschedule_event( $timestamp, 'actualizar_stock_bsale_event' );
        error_log('Evento desprogramado.');
    }
}
register_deactivation_hook( __FILE__, 'desactivar_actualizacion_stock_bsale_cron' );

/*----------------------------------------------------------------------------*/


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

function AjaxEndPointBsale(){
        echo file_get_contents(MY_PLUGIN_PATH_BSALE.'admin/dataBsaleCreate/DataBsale.json');
        wp_die();

}
add_action('wp_ajax_datatables_endpoint', 'AjaxEndPointBsale'); //logged in
add_action('wp_ajax_no_priv_datatables_endpoint', 'AjaxEndPointBsale'); //not logged in

// Función para obtener el ID de la variante en Bsale utilizando el SKU
function obtenerIdVariantePorSKU($sku, $token) {
    $url = "https://api.bsale.io/v1/variants.json?code=" . urlencode($sku);
    $headers = array(
        "access_token: $token",
        "Content-Type: application/json"
    );

    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => TRUE,
        CURLOPT_HTTPHEADER => $headers,
    ));

    $response = curl_exec($curl);
    curl_close($curl);

    $data = json_decode($response, true);
    if (isset($data['items'][0]['id'])) {
        return $data['items'][0]['id'];
    } else {
        return null;
    }
}

// Función para descontar stock en Bsale
function descontarStockBsale($order) {
    $productosConsumo = [];
    $token = "1e8466ceb957ded197876fe675c1bec1f16d79e4"; // Reemplaza con tu token de acceso
    $officeId = 1;  // ID de la sucursal en Bsale

    foreach ($order->get_items() as $item) {
        $product = $item->get_product();
        $quantity = $item->get_quantity();
        $item_sku = $product->get_sku();

        // Obtener el ID de la variante en Bsale utilizando el SKU
        $variantId = obtenerIdVariantePorSKU($item_sku, $token);

        if ($variantId) {
            // Añadir detalles del consumo de stock
            $productosConsumo[] = [
                'quantity' => $quantity,
                'variantId' => $variantId
            ];
        } else {
            // Manejar el caso en que no se encuentra la variante
            error_log("No se encontró la variante para el SKU: " . $item_sku);
        }
    }

    if (!empty($productosConsumo)) {
        $postdata = json_encode([
            'officeId' => $officeId,
            'note' => 'Descuento de stock por generación de boleta',
            'details' => $productosConsumo
        ]);

        $headers = array(
            "access_token: $token",
            "Content-Type: application/json"
        );

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.bsale.io/v1/stocks/consumptions.json",
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_POST  => true,
            CURLOPT_POSTFIELDS  => $postdata,
        ));

        $responseConsumo = curl_exec($curl);
        curl_close($curl);

        // Registrar en el log
        error_log(print_r($responseConsumo, true));
    } else {
        error_log("No se generó ningún consumo de stock debido a la falta de variantes válidas.");
    }
}
function PutOrderBsaleCreate2($order_id) {
    $order = wc_get_order($order_id);
    $id_order = $order->get_order_number();
    $statusOrden = $order->get_status();
    //$orderdate = strtotime($order->get_date_created());
    
    $orderdate = strtotime($order->get_date_completed());

    $correoBoleta = $order->get_billing_email();
    $productoOrdenArray = [];
    $totalAmount = 0;


foreach ($order->get_items() as $item_id => $item) {
    $product = $item->get_product();
    $quantity = $item->get_quantity();
    $item_sku = $product->get_sku();
    $precioConIVA = $item->get_total(); // Este ya es el subtotal (precio por cantidad con IVA)
    $precioSinIVA = $precioConIVA / 1.19; // Calcular precio sin IVA
    $product_name = $item->get_name();
    
    // Añadir este subtotal al totalAmount
    $totalAmount += $precioConIVA; // Aquí se acumula el subtotal de cada producto

    // Añadir cada producto al arreglo de detalles
    $productoOrdenArray[] = [
        'netUnitValue' => round($precioSinIVA / $quantity, 2), // Calcular precio unitario sin IVA
        'quantity' => $quantity,
        'comment' => $product_name,
        'taxes' => [
            [
                'code' => 14,
                'percentage' => 19
            ]
        ]
    ];
}

/*
    foreach ($order->get_items() as $item_id => $item) {
        $product = $item->get_product();
        $quantity = $item->get_quantity();
        $item_sku = $product->get_sku();
        $precioConIVA = $item->get_total();
        $precioSinIVA = $precioConIVA / 1.19; // Calcular precio sin IVA
        $product_name = $item->get_name();
        $totalAmount += $precioConIVA; // Sumar al total del pedido

        $productoOrdenArray[] = [
            'netUnitValue' => round($precioSinIVA, 2),
            'quantity' => $quantity,
            'comment' => $product_name,
            'taxes' => [
                [
                    'code' => 14,
                    'percentage' => 19
                ]
            ]
        ];
    }
*/
    // Construir el array de pagos
    $paymentsArray = [
        [
            'paymentTypeId' => 8, // ID para "TRANSFERENCIA BANCARIA"
            'amount' => round($totalAmount, 2)
        ]
    ];

    // Construir el cuerpo de la solicitud
    $postdata = json_encode([
        'codeSii' => 39, // Código SII para boleta electrónica
        'officeId' => 1, // ID de la oficina correspondiente
        'priceListId' => 8, // ID de la lista de precios
        'emissionDate' => $orderdate,
        'details' => $productoOrdenArray,
        'payments' => $paymentsArray
    ]);

    function generarBoleta($postdata) {
        $token = "1e8466ceb957ded197876fe675c1bec1f16d79e4";
        $headers = [
            "access_token: $token",
            "Content-Type: application/json"
        ];

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => "https://api.bsale.cl/v1/documents.json",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $postdata,
        ]);
        $responseBoleta = curl_exec($curl);
        curl_close($curl);
        return $responseBoleta;
        
    }

    $responseBoleta3 = generarBoleta($postdata);
    //$responseBoleta2 = '{"href":"https://api.bsale.cl/v1/documents/57426.json","id":57426,"emissionDate":1670976000,"expirationDate":1670976000,"generationDate":1670998346,"number":55367,"serialNumber":null,"trackingNumber":null,"totalAmount":473780.0,"netAmount":398134.0,"taxAmount":75646.0,"exemptAmount":0.0,"exportTotalAmount":0.0,"exportNetAmount":0.0,"exportTaxAmount":0.0,"exportExemptAmount":0.0,"commissionRate":0.0,"commissionNetAmount":0.0,"commissionTaxAmount":0.0,"commissionTotalAmount":0.0,"percentageTaxWithheld":0.0,"purchaseTaxAmount":0.0,"purchaseTotalAmount":0.0,"address":"","municipality":"","city":"","urlTimbre":null,"urlPublicView":"https://app2.bsale.cl/view/27264/c24f1c2ea561?sfd=99","urlPdf":"https://app2.bsale.cl/view/27264/c24f1c2ea561.pdf?sfd=99","urlPublicViewOriginal":"https://app2.bsale.cl/view/27264/c24f1c2ea561","urlPdfOriginal":"https://app2.bsale.cl/view/27264/c24f1c2ea561.pdf","token":"c24f1c2ea561","state":0,"commercialState":0,"urlXml":"https://api.bsale.cl/v1/27264/files/c24f1c2ea561.xml","ted":null,"salesId":null,"informedSii":2,"responseMsgSii":null,"document_type":{"href":"https://api.bsale.cl/v1/document_types/1.json","id":"1"},"office":{"href":"https://api.bsale.cl/v1/offices/4.json","id":"4"},"user":{"href":"https://api.bsale.cl/v1/users/4.json","id":"4"},"coin":{"href":"https://api.bsale.cl/v1/coins/1.json","id":"1"},"references":{"href":"https://api.bsale.cl/v1/documents/57426/references.json"},"document_taxes":{"href":"https://api.bsale.cl/v1/documents/57426/document_taxes.json"},"details":{"href":"https://api.bsale.cl/v1/documents/57426/details.json"},"sellers":{"href":"https://api.bsale.cl/v1/documents/57426/sellers.json"},"attributes":{"href":"https://api.bsale.cl/v1/documents/57426/attributes.json"}}';
    $boleta = json_decode($responseBoleta3);
    

    if (isset($boleta->urlPdf)) {
        
       
        
        $notas1 = 'Su boleta ha sido generada satisfactoriamente. Puede descargarla desde aquí: ' . $boleta->urlPdf;
        $order->add_order_note($notas1);
        $headers = 'From: Equipo de Soporte <clubreposterovitacura@gmail.com>' . "\r\n";
        wp_mail($correoBoleta, 'Envío de Boleta Bsale', $notas1, $headers);
        descontarStockBsale($order);
    } else {
        $order->add_order_note('Error al generar la boleta en Bsale.');
    }
    

    
}
add_action('woocommerce_order_status_completed', 'PutOrderBsaleCreate2', 10, 1);
