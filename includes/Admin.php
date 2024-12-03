<?php
/**
 * Admin options BsaleCommerce
 * @link        https://josecortesia.cl
 * @since       1.0.0
 * 
 * @package     base
 * @subpackage  base/include
 */

//funcion para registrar el menu del administrador

function CrearMenuBsaleCommerce() {
    add_menu_page( 
        'Configuración Bsale', 
        'BsaleCommerce', 
        'manage_options',
        'bsale_options', 
        'opciones_bsale',
        'dashicons-food',
        '65'
    );
}
add_action( 'admin_menu', 'CrearMenuBsaleCommerce' );


function opciones_bsale() {


    if ( !current_user_can( 'manage_options' ) )  {
        wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
    }

?>
    <div class="wrap">
        <h1><?php echo get_admin_page_title();  ?></h1>
    </div>

    <div class="wrap" style="max-width: 100%;margin: auto;padding: 35px;">
        <div class="card" style="max-width: 100%;">
            <h2 class="title">Productos BsaleCommerce</h2>
            <p class="submit">
            <input type="submit" name="submit" id="ActualizarJson" class="button button-primary" value="<?php  _e('Actualizar lista de productos', 'BsaleCommerce')?>">

            <input type="submit" name="submit" id="UpdateStockBsaleCommerce" class="button button-primary" value="<?php  _e('Actualizar Stock en Woocommerce', 'BsaleCommerce')?>">

            <input type="submit" style="display:none;" name="submit" id="UpdatePrecioBsaleCommerce" class="button button-primary" value="<?php  _e('Actualizar Precio en Woocommerce', 'BsaleCommerce')?>">

            <input type="submit" style="display:none;" name="submit" id="UpdatePrecioIvaBsaleCommerce" class="button button-primary" value="<?php  _e('Actualizar Precio con IVA en Woocommerce', 'BsaleCommerce')?>">



            </p>

        </div>

        <div class="table card" style="max-width: 100%;">
            <!--
            <table id="myTablebsale" class="display" style="width:100%">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Desc</th>
                        <th>ID Tipo</th>
                        <th>Tipo</th>


                    </tr>
                </thead>
                <tbody>
                    
                </tbody>
                <tfoot>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Desc</th>
                        <th>ID Tipo</th>
                        <th>Tipo</th>


                    </tr>
                </tfoot>
            </table>
            -->
            <table id="myTablebsale" class="display" style="width:100%">
                <thead>
                    <tr>
                        <th>Sku</th>
                        <th>Nombre</th>
                        <th>stock</th>
                        <th>Precio</th>
                        <th>Precio + IVA</th>
                    </tr>
                </thead>
                <tbody>
                    
                </tbody>
                <tfoot>
                    <tr>
                        <th>Sku</th>
                        <th>Nombre</th>
                        <th>stock</th>
                        <th>Precio</th>
                        <th>Precio + IVA</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>




<div class="popup-overlay"></div>
<div class="loaderBsaleCommerce centeredBsaleCommerce" style='display: none;'></div>



<?php

}


 



