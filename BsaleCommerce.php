<?php
/**
 * Bsale con Bsalecommerce
 *
 * Plugin Name: BsaleCommerce
 * Plugin URI:  https://dotdevsolutions.com
 * Description: Integración de bsale para Bsalecommerce.
 * Version:     1.0
 * Author:      José Vega
 * Author URI:  https://dotdevsolutions.com
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
    wp_enqueue_style('font-awesome.min', plugins_url('admin/vendor/font-awesome/font-awesome.min.css',__FILE__), array(), '4.7.0', 'all');
    wp_enqueue_style('dataTables.dataTables.min', plugins_url('admin/datatable/dataTables.dataTables.min.css',__FILE__), array(), '1.3.3', 'all');
    wp_enqueue_script('AdminBsaleCommerce', plugins_url('admin/js/AdminBsaleCommerce.js',__FILE__), array('jquery'), '1.12.4');
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

function ActualizarStockBsale($page = 0, $is_cron = false) {
    $nonce = isset($_POST['nonce']) ? sanitize_text_field($_POST['nonce']) : '';
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

add_action( 'wp_ajax_updatebsale', 'ActualizarStockBsale' );


/*CRON JV*/

// Hook para el evento de WP-Cron, ejecuta la función de actualización
add_action('actualizar_stock_bsale_event', 'ActualizarStockBsale', 10, 2);

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
function obtenerIdVariantePorSKU($sku, $token) {    $url = "https://api.bsale.io/v1/variants.json?code=" . urlencode($sku);
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

function obtenerSerialNumberPorVariantID($variantId, $token) {   $url = "https://api.bsale.io/v1/variants/" . urlencode($variantId) . "/serials.json?officeid=1";
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
    if (isset($data['items'][0]['serialNumber'])) {
        return $data['items'][0]['serialNumber'];
    } else {
        return 0; // String vacío si no encuentra
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
        $serialNumber = obtenerSerialNumberPorVariantID($variantId, $token);

        if ($variantId) {
            // Añadir detalles del consumo de stock
            if ($serialNumber == 0 || $serialNumber == '0') {
                $productosConsumo[] = [
                    'quantity' => $quantity,
                    'variantId' => $variantId
                ];
            } else {
                $productosConsumo[] = [
                    'quantity' => $quantity,
                    'variantId' => $variantId,
                    'serialNumber' => $serialNumber
                ];
            }
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
        
        // Registrar en el log con texto identificable
        error_log("=== CONSUMO BSALE RESPONSE ===");
        error_log("URL: https://api.bsale.io/v1/stocks/consumptions.json");
        error_log("Postdata: " . $postdata);
        error_log("Response: " . print_r($responseConsumo, true));
        error_log("=== FIN CONSUMO BSALE ===");
    } else {
        error_log("No se generó ningún consumo de stock debido a la falta de variantes válidas.");
    }
}

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
    // Para la respuesta CURL simple
    return $responseBoleta;
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
    $shipping_total = $order->get_shipping_total();
    
    foreach ($order->get_items() as $item_id => $item) {
        $product = $item->get_product();
        $quantity = $item->get_quantity();
        $item_sku = $product->get_sku();
        $precioConIVA = $item->get_total(); // Este ya es el subtotal (precio por cantidad con IVA)
        $precioSinIVA = $precioConIVA / 1.19; // Calcular precio sin IVA
        $product_name = $item->get_name();
        
        // Añadir este subtotal al totalAmount
        $totalAmount += $precioConIVA; // Aquí se acumula el subtotal de cada producto
        // Procesar el costo de envío
        //$shipping_total = $order->get_shipping_total();
        $shipping_method = $order->get_shipping_method();
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

    // Procesar el costo de envío
    if ($shipping_total > 0 ) {
        $shippingSinIVA = $shipping_total / 1.19; // Calcular precio sin IVA para el envío

        $productoOrdenArray[] = [
            'netUnitValue' => round($shippingSinIVA, 2),
            'quantity' => 1,
            'comment' => 'Costo de envío',
            'taxes' => [
                [
                    'code' => 14,
                    'percentage' => 19
                ]
            ]
        ];

        $totalAmount += $shipping_total; // Sumar el costo de envío al total
    }

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

    $responseBoleta3 = generarBoleta($postdata);
    //$responseBoleta2 = '{"href":"https://api.bsale.cl/v1/documents/57426.json","id":57426,"emissionDate":1670976000,"expirationDate":1670976000,"generationDate":1670998346,"number":55367,"serialNumber":null,"trackingNumber":null,"totalAmount":473780.0,"netAmount":398134.0,"taxAmount":75646.0,"exemptAmount":0.0,"exportTotalAmount":0.0,"exportNetAmount":0.0,"exportTaxAmount":0.0,"exportExemptAmount":0.0,"commissionRate":0.0,"commissionNetAmount":0.0,"commissionTaxAmount":0.0,"commissionTotalAmount":0.0,"percentageTaxWithheld":0.0,"purchaseTaxAmount":0.0,"purchaseTotalAmount":0.0,"address":"","municipality":"","city":"","urlTimbre":null,"urlPublicView":"https://app2.bsale.cl/view/27264/c24f1c2ea561?sfd=99","urlPdf":"https://app2.bsale.cl/view/27264/c24f1c2ea561.pdf?sfd=99","urlPublicViewOriginal":"https://app2.bsale.cl/view/27264/c24f1c2ea561","urlPdfOriginal":"https://app2.bsale.cl/view/27264/c24f1c2ea561.pdf","token":"c24f1c2ea561","state":0,"commercialState":0,"urlXml":"https://api.bsale.cl/v1/27264/files/c24f1c2ea561.xml","ted":null,"salesId":null,"informedSii":2,"responseMsgSii":null,"document_type":{"href":"https://api.bsale.cl/v1/document_types/1.json","id":"1"},"office":{"href":"https://api.bsale.cl/v1/offices/4.json","id":"4"},"user":{"href":"https://api.bsale.cl/v1/users/4.json","id":"4"},"coin":{"href":"https://api.bsale.cl/v1/coins/1.json","id":"1"},"references":{"href":"https://api.bsale.cl/v1/documents/57426/references.json"},"document_taxes":{"href":"https://api.bsale.cl/v1/documents/57426/document_taxes.json"},"details":{"href":"https://api.bsale.cl/v1/documents/57426/details.json"},"sellers":{"href":"https://api.bsale.cl/v1/documents/57426/sellers.json"},"attributes":{"href":"https://api.bsale.cl/v1/documents/57426/attributes.json"}}';
    $boleta = json_decode($responseBoleta3);
    

    if (isset($boleta->urlPdf)) {
        $notas1 = 'Su boleta ha sido generada satisfactoriamente. Puede descargarla desde aquí: ' . $boleta->urlPdf;
        $order->add_order_note($notas1);
        $headers = 'From: Equipo de Soporte <clubreposterovitacura@gmail.com>' . "\r\n";
        descontarStockBsale($order);
        wp_mail($correoBoleta, 'Envío de Boleta Bsale', $notas1, $headers);
    } else {
        $order->add_order_note('Error al generar la boleta en Bsale.');
    }
    

    
}
add_action('woocommerce_order_status_completed', 'PutOrderBsaleCreate2', 10, 1);

add_action('woocommerce_order_status_changed', 'generar_factura_bsale', 10, 4);

function generar_factura_bsale($order_id, $old_status, $new_status, $order) {
    // Verificar si el nuevo estado es 'pending'
    if ($new_status !== 'pending') {
        return;
    }

    // Obtener el valor del campo personalizado 'billing_factura'
    $billing_factura = get_post_meta($order_id, 'billing_factura', true);

    // Verificar si el cliente solicitó factura
    if ($billing_factura != '1') {
        return;
    }

    // Obtener datos del cliente y de la orden
    $rut_empresa = get_post_meta($order_id, 'rut_empresa', true);
    $razon_social = get_post_meta($order_id, 'razon', true);
    $giro = get_post_meta($order_id, 'giro', true);
    $direccion = get_post_meta($order_id, 'direccion', true);
    $ciudad = $order->get_billing_city();
    $comuna = $order->get_billing_state();
    $telefono = $order->get_billing_phone();
    $email = $order->get_billing_email();

    // Preparar los detalles de los productos
    $items = $order->get_items();
    $detalles = [];

    foreach ($items as $item) {
        $producto = $item->get_product();
        $sku = $producto->get_sku();
        $cantidad = $item->get_quantity();
        $precio_unitario = $item->get_total() / $cantidad;
        $precio_neto = $precio_unitario / 1.19; // Asumiendo un IVA del 19%

        // Agregar detalle al array
        $detalles[] = [
            'code' => $sku,
            'quantity' => $cantidad,
            'netUnitValue' => $precio_neto,
            'taxes' => [
                [
                    'code' => 14, // Código del impuesto según Bsale
                    'percentage' => 19
                ]
            ],
            'comment' => $item->get_name()
        ];
    }

    // Preparar los datos para la API de Bsale
    $data = [
        'documentTypeId' => 5, // ID del tipo de documento para Factura Electrónica Térmica
        'officeId' => 1, // ID de la sucursal en Bsale
        'priceListId' => 8, // ID de la lista de precios a utilizar
        'emissionDate' => strtotime($order->get_date_created()),
        'client' => [
            'code' => $rut_empresa,
            'company' => $razon_social,
            'activity' => $giro,
            'address' => $direccion,
            'municipality' => $comuna,
            'city' => $ciudad,
            'phone' => $telefono,
            'email' => $email,
            'companyOrPerson' => 1 // 1 indica que es una empresa
        ],
        'details' => $detalles
    ];

    // Enviar la solicitud a la API de Bsale
    $response = wp_remote_post('https://api.bsale.cl/v1/documents.json', [
        'headers' => [
            'Content-Type' => 'application/json',
            'access_token' => '1e8466ceb957ded197876fe675c1bec1f16d79e4'
        ],
        'body' => json_encode($data)
    ]);

    // Manejar la respuesta de la API
    if (is_wp_error($response)) {
        error_log('Error al conectar con la API de Bsale: ' . $response->get_error_message());
    } else {
        $body = wp_remote_retrieve_body($response);
        $result = json_decode($body, true);

        if (isset($result['id'])) {
            // Factura generada exitosamente
            update_post_meta($order_id, '_bsale_invoice_id', $result['id']);
            $order->add_order_note('Factura generada exitosamente. ID de Bsale: ' . $result['id']);

            // Agregar la URL pública de la factura a las notas del pedido
            if (isset($result['urlPublicView'])) {
                $order->add_order_note('Puede ver la factura en el siguiente enlace: ' . $result['urlPublicView']);
            }
        } else {
            // Error al generar la factura
            error_log('Error al generar la factura en Bsale: ' . $body);
            $order->add_order_note('Error al generar la factura en Bsale. Verifique los registros para más detalles.');
        }
    }
}

/**
 * Plugin Name: Actualizador de Stock Bsale con Cron Externo
 */

// Registrar endpoint para cron de cPanel
add_action('init', 'registrar_endpoint_bsale_cron');
function registrar_endpoint_bsale_cron() {
    add_rewrite_rule('^cron-bsale-stock/?$', 'index.php?cron_bsale_stock=1', 'top');
    add_rewrite_tag('%cron_bsale_stock%', '([^&]+)');
}

// Registrar variable de query
add_action('query_vars', 'registrar_query_vars_bsale');
function registrar_query_vars_bsale($vars) {
    $vars[] = 'cron_bsale_stock';
    return $vars;
}

// Manejar la solicitud del cron
add_action('template_redirect', 'manejar_cron_bsale');
function manejar_cron_bsale() {
    if (get_query_var('cron_bsale_stock')) {
        // Verificar seguridad con token
        $token = isset($_GET['token']) ? sanitize_text_field($_GET['token']) : '';
        if ($token !== 'PBZld.z8pyZkCaSWtonjDe3r.4zTgAL') {
            status_header(403);
            exit('Acceso no autorizado');
        }
        
        // Obtener parámetros
        $page = isset($_GET['page']) ? intval($_GET['page']) : 0;
        $force = isset($_GET['force']) ? boolval($_GET['force']) : false;
        
        // Ejecutar la actualización
        ActualizarStockBsaleCron($page, $force);
        exit;
    }
}

// Función para el cron de cPanel - Procesa TODAS las páginas
function ActualizarStockBsaleCron($page = 0, $force = false) {
    // Validar token de seguridad primero
    $token = isset($_GET['token']) ? sanitize_text_field($_GET['token']) : '';
    $valid_token = 'PBZld.z8pyZkCaSWtonjDe3r.4zTgAL';
    
    if ($token !== $valid_token) {
        status_header(403);
        exit('Acceso no autorizado. Token incorrecto.');
    }
    
    // Detectar si es ejecución desde cPanel
    $is_cpanel_cron = !isset($_GET['page']);
    
    // Habilitar logging
    error_log("=== INICIANDO ACTUALIZACIÓN BSALE - Página $page ===");
    
    // Deshabilitar errores de base de datos temporalmente para evitar "Commands out of sync"
    $original_wpdb_show_errors = $GLOBALS['wpdb']->show_errors;
    $GLOBALS['wpdb']->show_errors = false;
    
    try {
        $Cla = new CurlRequestBsale();
        $updated_total = 0;
        $pages_processed = 0;
        $hasMorePages = true;
        
        // Procesar TODAS las páginas en una sola ejecución
        while ($hasMorePages) {
            error_log("Procesando página $page");
            
            $newStocks = json_decode($Cla->getProductsStocks($page), true);
            $hasMorePages = count($newStocks) > 0;
            
            if (!$hasMorePages) {
                error_log("No hay más páginas por procesar");
                break;
            }
            
            // Procesar los stocks de esta página
            $updated_count = 0;
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
                    
                    $updated_count++;
                    
                    //error_log("Actualizado producto ID: $product_id, SKU: $sku, Stock: $stock");
                    
                    // Liberar memoria periódicamente
                    if ($updated_count % 20 === 0) {
                        wp_cache_flush();
                    }
                }
            }
            
            $updated_total += $updated_count;
            $pages_processed++;
            
            error_log("Página $page completada. $updated_count productos actualizados.");
            
            $page++;
            
            // Pequeña pausa de 2 segundos entre páginas
            if ($hasMorePages) {
                sleep(2);
                
                // Cerrar conexiones de base de datos periódicamente
                if ($pages_processed % 5 === 0) {
                    if (function_exists('mysqli_close') && isset($GLOBALS['wpdb']->dbh)) {
                        @mysqli_close($GLOBALS['wpdb']->dbh);
                    }
                    // Forzar reconexión
                    $GLOBALS['wpdb']->db_connect();
                }
            }
        }

        // Log final
        error_log("=== ACTUALIZACIÓN BSALE COMPLETADA ===");
        error_log("Total: $pages_processed páginas procesadas, $updated_total productos actualizados");
        
        // Restaurar configuración de errores de base de datos
        $GLOBALS['wpdb']->show_errors = $original_wpdb_show_errors;
        
        // Para cPanel: Solo logging, sin output
        if ($is_cpanel_cron) {
            exit();
        }
        
        // Para navegador: Mostrar resultados
        echo "Actualización COMPLETADA. $pages_processed páginas procesadas, $updated_total productos actualizados.";

    } catch (Exception $e) {
        // Restaurar configuración de errores de base de datos
        $GLOBALS['wpdb']->show_errors = $original_wpdb_show_errors;
        
        $error_msg = "Error en página $page: " . $e->getMessage();
        error_log($error_msg);
        
        if (!$is_cpanel_cron) {
            echo $error_msg;
        }
    }
}


// Limpiar reglas de rewrite
register_deactivation_hook(__FILE__, 'limpiar_rewrite_rules_bsale');
function limpiar_rewrite_rules_bsale() {
    flush_rewrite_rules();
}

// Asegurar que las reglas de rewrite estén activas
register_activation_hook(__FILE__, 'activar_plugin_bsale_cron');
function activar_plugin_bsale_cron() {
    registrar_endpoint_bsale_cron();
    flush_rewrite_rules();
}

//add_action('woocommerce_admin_order_items_after_line_items', 'simple_debug_order_quantities', 10, 1);

function simple_debug_order_quantities($order_id) {
    $order = wc_get_order($order_id);
    
    if (!$order) {
        return;
    }
    
    echo '<div class="debug-quantities" style="background: #e7f3ff; padding: 15px; margin: 20px 0; border: 1px solid #b3d9ff;">';
    echo '<h4>📊 Cantidades de Productos</h4>';
    
    foreach ($order->get_items() as $item_id => $item) {
        $quantity = $item->get_quantity();
        echo '<p><strong>' . $item->get_name() . ':</strong> ';
        var_dump($quantity);
        echo ' (tipo: ' . gettype($quantity) . ')</p>';
    }
    
    echo '</div>';
}