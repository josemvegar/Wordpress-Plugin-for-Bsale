<?php
/**
 * Bsale Admin Clases plugin
 * @link        https://josecortesia.cl
 * @since       1.0.0
 * 
 * @package     base
 * @subpackage  base/include
 */

class CurlRequestBsale {


    /* Funciones que generan los JSON solicitados
    --getProducts (Listado de productos)
    --getProductsStocks (Stock de los productos, según sus variables, y la sucursal en la que se encuentran)
    --getPriceList
*/



    public function buildProductsJson(){
        $token = "a1db9545ead1ad4181370c635f1bbf556e04b5b0";
        $headers = array(
            "access_token: $token",
            "Content-Type: application/json"
        );

        $products = json_decode($this->getProducts());
            /*foreach($products as $product){
                //$product->product_type = json_decode(getProductTypeById($headers, $product->product_type_id))->name;
                $product->variants =json_decode($this->getVariantsByProduct($product->id));
            }*/
            return json_encode($products);
    }

    public function getProducts(){


    $token = "a1db9545ead1ad4181370c635f1bbf556e04b5b0";
    $headers = array(
        "access_token: $token",
        "Content-Type: application/json"
    );

        $counter = 0;
        $jsonProducts = [];
        //do{
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://api.bsale.cl/v1/products.json?offset=$counter",
                CURLOPT_HTTPGET => true,
                CURLOPT_RETURNTRANSFER => TRUE,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => $headers,
            ));
            $response = curl_exec($curl);
            if(curl_errno($curl)){
                curl_error($curl);
                curl_close($curl);
                return false;
            }else{
                $products = json_decode($response,true);
                curl_close($curl);
                $count = $products->count;
                $i = 0;
                foreach($products['items'] as $product){                    
                    //$jsonProducts[$i + $counter]=[
                    $variant = self::getVariantsByProduct($product['id']);
                    $jsonProducts[]=[
                        "id" => $product['id'],
                        "name" => $product['name'],
                        // "description" => $product->description,
                        //"product_type_id" => $product->product_type->id,
                        "counter" => $counter,
                        //"product_type" => json_encode(getProductTypeById($headers, $product->product_type->id)['name']),
                        "variantId" => $variant["id"],
                        "sku" => $variant["sku"],
                        "stock" => self::getStockByVariant($variant["id"]),
                        "price" => self::getPriceByVariant($variant["id"])
                    ];
                    $i++;
                    $counter++;
                }
                //json_encode($jsonProducts, JSON_PRETTY_PRINT);
            }
        //}while($counter < $count);
        return json_encode($jsonProducts, JSON_PRETTY_PRINT);
    }

    public function getVariantsByProduct($id){
        $token = "a1db9545ead1ad4181370c635f1bbf556e04b5b0";
        $headers = array(
            "access_token: $token",
            "Content-Type: application/json"
        );
            $jsonVariants = [];
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://api.bsale.cl/v1/products/$id/variants.json",
                CURLOPT_HTTPGET => true,
                CURLOPT_RETURNTRANSFER => TRUE,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => $headers,
            ));
            $response = curl_exec($curl);
            if(curl_errno($curl)){
                curl_error($curl);
                curl_close($curl);
                return false;
            }else{
                $variants = json_decode($response,true);
                curl_close($curl);
                $i = 0;
                //foreach($variants['items'] as $variant){
                    $jsonVariants=[
                        "id" => $variants['items'][0]['id'],
                        "sku" => $variants['items'][0]['code']
                        //"stock" => json_decode($this->getStockByVariant($variant['id'])),
                    ];
                    $i++;
                //}
                //json_encode($jsonVariants, JSON_PRETTY_PRINT);
            }
            //return json_encode($jsonVariants);
            return $jsonVariants;
        }

    public function getStockByVariant($id){
        $token = "a1db9545ead1ad4181370c635f1bbf556e04b5b0";
        $headers = array(
            "access_token: $token",
            "Content-Type: application/json"
        );
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://api.bsale.cl/v1/stocks.json?variantid=$id",
                CURLOPT_HTTPGET => true,
                CURLOPT_RETURNTRANSFER => TRUE,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => $headers,
            ));
            $response = curl_exec($curl);
            if(curl_errno($curl)){
                curl_error($curl);
                curl_close($curl);
                return false;
            }else{
                $stocks = json_decode($response,true);
                curl_close($curl);
                $jsonStocks = 0;
                $i = 0;
                $stock = $stocks["items"][0]["quantityAvailable"];
                //foreach($stocks['items'] as $stock){
                    /*$jsonStocks[$i] = [
                        "stock_id" => $stock['id'],
                        "quantity" => $stock['quantity'],
                        "quantityReserved" => $stock['quantityReserved'],
                        "quantityAvailable" => $stock['quantityAvailable'],
                        "office_id" => $stock['office']['id'],
                        "office_name" => $this->getOfficeById( $stock['office']['id'])['name'],
                    ];
                    $i++;
                    json_encode($jsonStocks, JSON_PRETTY_PRINT);*/
                //}
                //json_encode($jsonStocks, JSON_PRETTY_PRINT);
            }
            //return json_encode($jsonStocks);
            return $stock;
        }
        public function getPriceByVariant($id){
            $token = "a1db9545ead1ad4181370c635f1bbf556e04b5b0";
            $headers = array(
                "access_token: $token",
                "Content-Type: application/json"
            );
                $curl = curl_init();
                curl_setopt_array($curl, array(
                    CURLOPT_URL => "https://api.bsale.cl/v1/price_lists/6/details.json?variantid=$id",
                    CURLOPT_HTTPGET => true,
                    CURLOPT_RETURNTRANSFER => TRUE,
                    CURLOPT_CUSTOMREQUEST => 'GET',
                    CURLOPT_HTTPHEADER => $headers,
                ));
                $response = curl_exec($curl);
                if(curl_errno($curl)){
                    curl_error($curl);
                    curl_close($curl);
                    return false;
                }else{
                    $prices = json_decode($response,true);
                    curl_close($curl);
                    $i = 0;
                    $price = $prices["items"][0]["variantValueWithTaxes"];
                    
                }
                return $price;
            }

    /*public function getVariants(){
    $token = "b6a48e1fcf8eb588e1b6c66446b7d87baf0660c0";
    $headers = array(
        "access_token: $token",
        "Content-Type: application/json"
    );
        $counter = 0;
        $jsonVariants = [];
        do{

            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://api.bsale.cl/v1/variants.json?&offset=$counter",
                CURLOPT_HTTPGET => true,
                CURLOPT_RETURNTRANSFER => TRUE,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => $headers,
            ));
            $response = curl_exec($curl);
            if(curl_errno($curl)){
                curl_error($curl);
                curl_close($curl);
                return false;
            }else{
                $variants = json_decode($response);
                curl_close($curl);
                $count = $variants->count;
                $i = 0;
                foreach($variants->items AS $variant){
                    $jsonVariants[$i + $counter] = [
                        "id" => $variant->id,
                        "description" => $variant->description,
                        "code" => $variant->code,
                        "counter" => $counter
                    ];
                    $i++;
                    $counter++;
                    json_encode($jsonVariants, JSON_PRETTY_PRINT);
                }
            }
        }while($counter < $count);
        return json_encode($jsonVariants);
    }
    public function getProductTypes(){

    $token = "b6a48e1fcf8eb588e1b6c66446b7d87baf0660c0";
    $headers = array(
        "access_token: $token",
        "Content-Type: application/json"
    );
        $jsonProductTypes = [];
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.bsale.cl/v1/product_types.json?",
            CURLOPT_HTTPGET => true,
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => $headers,
        ));
        $response = curl_exec($curl);
        if(curl_errno($curl)){
            curl_error($curl);
            curl_close($curl);
            return false;
        }else{
            $productTypes = json_decode($response);
            curl_close($curl);
            $i=0;
            foreach($productTypes->items AS $productType){
                $jsonProductTypes[$i]=[
                    "id" => $productType->id,
                    "name" => $productType->name,
                ];
            }
            json_encode($jsonProductTypes, JSON_PRETTY_PRINT);
        }
        return json_encode($jsonProductTypes);
    }
    public function getStocks(){ 
    $token = "b6a48e1fcf8eb588e1b6c66446b7d87baf0660c0";
    $headers = array(
        "access_token: $token",
        "Content-Type: application/json"
    );
        $jsonStocks = [];
        $counter = 0;
        do{
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://api.bsale.cl/v1/stocks.json?offset=$counter",
                CURLOPT_HTTPGET => true,
                CURLOPT_RETURNTRANSFER => TRUE,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => $headers,
            ));
    
            $response = curl_exec($curl);
            if(curl_errno($curl)){
                curl_error($curl);
                curl_close($curl);
                return false;
            }else{
                $stocks = json_decode($response);
                curl_close($curl);
                $count = $stocks->count;
                $i = 0;
                foreach($stocks->items AS $stock){
                    $jsonStocks[$i] = [
                        "id" => $stock->id,
                        "quantity" => $stock->quantity,
                        "quantityReserved" => $stock->quantityReserved,
                        "quantityAvailable" => $stock->quantityAvailable,
                        "variant_id" => $stock->variant->id,
                    ];
                    $i++;
                    $counter++;
                    json_encode($jsonStocks, JSON_PRETTY_PRINT);
                }
            }
        }while($counter < $count);
        return json_encode($jsonStocks);
    }

    public function getPriceLists(){
    $token = "b6a48e1fcf8eb588e1b6c66446b7d87baf0660c0";
    $headers = array(
        "access_token: $token",
        "Content-Type: application/json"
    );
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.bsale.cl/v1/price_lists.json?",
            CURLOPT_HTTPGET => true,
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => $headers,
        ));

        $response = curl_exec($curl);
        if(curl_errno($curl)){
            curl_error($curl);
            curl_close($curl);
            return false;
        }else{
            $priceLists = json_decode($response);
            curl_close($curl);
            $jsonPriceLists = [];
            $i = 0;
            foreach($priceLists->items AS $priceList){
                $jsonPriceLists[$i] = [
                    "id" => $priceList->id,
                    "name" => $priceList->name,
                    "state" => $priceList->state,
                    //"details" => json_decode($this->getPriceListDetails($headers, $priceList->id)),
                ];
                $i++;
                json_encode($jsonPriceLists, JSON_PRETTY_PRINT);
            }
        }
        return json_encode($jsonPriceLists);
    }

    public function getPriceListsDetails( $id , $variants){
    $token = "b6a48e1fcf8eb588e1b6c66446b7d87baf0660c0";
    $headers = array(
        "access_token: $token",
        "Content-Type: application/json"
    );
        $jsonPriceListDetails = [];
        $counter = 0;
        $variants = json_decode($variants);
        do{
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://api.bsale.cl/v1/price_lists/$id/details.json?offset=$counter",
                CURLOPT_HTTPGET => true,
                CURLOPT_RETURNTRANSFER => TRUE,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => $headers,
            ));
            $response = curl_exec($curl);
            if(curl_errno($curl)){
                curl_error($curl);
                curl_close($curl);
                return false;
            }else{
                $priceListDetails = json_decode($response);
                $count = $priceListDetails->count;
                $i = 0;
                foreach($priceListDetails->items AS $priceListDetail){
                    if(isset($variants)){
                        foreach( $variants as $variant){
                            if($variant->id == $priceListDetail->variant->id){
                                $variant_code = $variant->code;
                            }
                        }
                    }
                    $jsonPriceListDetails[$i + $counter] = [
                        "id" => $priceListDetail->id,
                        "variant_id" => $priceListDetail->variant->id,
                        "variant_code" => $variant_code,
                        "variantValue" => $priceListDetail->variantValue,
                        "variantValueWithTaxes" => $priceListDetail->variantValueWithTaxes,
                    ];
                    $i++;
                    $counter++;
                    json_encode($jsonPriceListDetails, JSON_PRETTY_PRINT);
                }
            }
        }while($counter < $count);
        curl_close($curl);
        return json_encode($jsonPriceListDetails);
    }

    public function getVariantById($id){
    $token = "b6a48e1fcf8eb588e1b6c66446b7d87baf0660c0";
    $headers = array(
        "access_token: $token",
        "Content-Type: application/json"
    );
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.bsale.cl/v1/variants/$id.json",
            CURLOPT_HTTPGET => true,
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => $headers,
        ));
        $response = curl_exec($curl);
        if(curl_errno($curl)){
            curl_error($curl);
            return false;
        }else{
            $variants = json_decode($response);
            $jsonVariants = [];
                $jsonVariants=[
                    "id" => $variants->id,
                    "description" => $variants->description,
                    "code" => $variants->code,
                ];
            json_encode($jsonVariants, JSON_PRETTY_PRINT);
        }
        curl_close($curl);
        return ($jsonVariants);
    }

    public function getProductTypeById( $id){
    $token = "b6a48e1fcf8eb588e1b6c66446b7d87baf0660c0";
    $headers = array(
        "access_token: $token",
        "Content-Type: application/json"
    );
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.bsale.cl/v1/product_types/$id.json",
            CURLOPT_HTTPGET => true,
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => $headers,
        ));
        $response = curl_exec($curl);
        if(curl_errno($curl)){
            curl_error($curl);
            curl_close($curl);
            return false;
        }else{
            $offices = json_decode($response);
            curl_close($curl);
            $jsonOffices = [];
                $jsonOffices=[
                    "id" => $offices->id,
                    "name" => $offices->name,
                ];
            json_encode($jsonOffices, JSON_PRETTY_PRINT);
        }
        return json_encode($jsonOffices);
    }

    public function getOfficeById($id){
    $token = "a1db9545ead1ad4181370c635f1bbf556e04b5b0";
    $headers = array(
        "access_token: $token",
        "Content-Type: application/json"
    );
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.bsale.cl/v1/offices/$id.json",
            CURLOPT_HTTPGET => true,
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => $headers,
        ));
        $response = curl_exec($curl);
        if(curl_errno($curl)){
            curl_error($curl);
            curl_close($curl);
            return false;
        }else{
            $offices = json_decode($response,true);
            curl_close($curl);
            $jsonOffices = [];
                $jsonOffices=[
                    "id" => $offices['id'],
                    "name" => $offices['name'],
                ];
            json_encode($jsonOffices, JSON_PRETTY_PRINT);
        }
        return ($jsonOffices);
    }

    public function buildPriceListsJson(){

    $token = "b6a48e1fcf8eb588e1b6c66446b7d87baf0660c0";
    $headers = array(
        "access_token: $token",
        "Content-Type: application/json"
    );
        $variants = $this->getVariants();
        $priceLists = json_decode($this->getPriceLists());

        foreach($priceLists as $priceList){
            $priceList->details = json_decode($this->getPriceListsDetails($priceList->id, $variants));
        }
        return json_encode($priceLists);
    }

    public function generarBoleta($postdata){

    $token = "b6a48e1fcf8eb588e1b6c66446b7d87baf0660c0";
    $headers = array(
        "access_token: $token",
        "Content-Type: application/json"
    );

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.bsale.cl//v1/documents.json",
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_POST  => true,
            CURLOPT_POSTFIELDS  => $postdata,
        ));
        $responseBoleta = curl_exec($curl);
        curl_close($curl);
        return $responseBoleta;


    }*/

}