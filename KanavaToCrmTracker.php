<?php

/**
 * 
 * Description of Kanava TO Crm Tracker
 *
 * @author Heikki Pals, 
 *
 */
abstract class KanavaToCrmTracker {

    var $responce = '';

    /**
     *
     * 	This is customer specific tracking id and shold be saved in module settings
     *
     */
    protected static $public_trackingID = null;
    
    /**
    *   Tracking data.
    *
    */
    private static $tracking_data = array(
        'pt_id' => null, // REQUIRED. public tracking ID  , from  SPOTMORE.fi service provider , e.g. 130000001
        'locale' => 'fi', // Locale for all descriptions ( article / group / ... ) 
        'customer_id' => '', // e-commerce db customer id 
        'visited_url' => '', //  = 'https://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
        'ref_url' => '', // reference url, previous page customer was coming from:  isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
        'web_browser' => '', //  = $_SERVER['HTTP_USER_AGENT'];
        //'platform' => '',
        'visited_from_ip' => '', // = $_SERVER['REMOTE_ADDR'];
        'visited_from_host' => '', // = gethostbyaddr($_SERVER['REMOTE_ADDR']);
        'Lead' => array(// customer information
            'lead_company' => '', // Lead company name
            'lead_firstname' => '', // Lead First name
            'lead_lastname' => '', // Lead Last name
            'lead_email' => '', // REQUIRED. Lead Email address, minimum requirement!
            'lead_externalid' => null, // e-commerce  custmer id from its DB
            'lead_phone_number' => null, // e-commerce  custmer lead_phone_number from its DB
            'lead_city' => null, // e-commerce  custmer lead_city from its DB
            'lead_postalcode' => null, // e-commerce  custmer lead_postalcode from its DB. MAX 5 digits
            'lead_street_address' => null, // e-commerce  custmer lead_street_address from its DB
            'allow_direct_marketing' => 0, // 0 or 1. Allow direct marketing, newsletter subscriptions
            // safe to set 1, if customer has requested newsletter.
            // for Guest accounts this is certainly 0	
            'LeadGroup' => array(// E-commerce customer groups
            /*
              0 => array(                 // optional
              'group_externalid' => null ,   // e-commerce group id from its DB
              'group_name' => null        // customer gruop name
              ),
              1 =>  array(                      // optional
              'group_externalid' => null ,   // e-commerce group id from its DB,
              'group_name' => null        //  customer gruop name
              ), */
            )
        ),
        'LeadNote' => array(// , optional , not mandatory,  This is used, if customer fills contact forms.
        //0 => array(
        //  'note'   => 'Contact  form field name 1: Customer Answer 1'
        //),
        //1 => array(
        //    'note'   => 'Contact  form field name 2: Customer Answer 2'
        //),
        // X - number of field / answers
        ),
        'Article' => array(
            // visting  article  details page  
            // this key is presented if customer is visitng a product details page
            'ecommerce_article_id' => null, // 
            'article_title' => ' product title with model ', //  product title,
            'article_fhd_imge_url' => 'https://eshop.domain.com/images/1920x1080_image.jpg', // optional url to article main image, jpg or png
            'is_active' => 1, //  not mandatory key.  Client site can deactivate old products by making generic API calls.
        /*
          'ArticleGroup' => array(// product group  name, not mandatory
          0 => array(// optional
          'group_externalid' => 1, // e-commerce group id from its DB,
          'group_external_parentid' => null, // e-commerce group id parent group id, first group is root group
          'group_title' => null           // product group  name
          ),
          1 => array(// seuraava
          'group_externalid' => 2, // e-commerce group id from its DB
          'group_external_parentid' => 1, // e-commerce group id parent group id  , subgroup of root group.
          'group_title' => null       // product group  name
          ),
          //   n  number of product groups
          )
         */
        ),
        'Cart' => array(
        // any page request, ajax on not , with customer cart content , 
        // 
        // if we are  tracking customer cart content.
        /*
          0 => array(
          'ecommerce_article_id' => '' , // varchar (36)  magento product id, unique with product feature combinations
          'article_title' => ' product title with model ',     //  product title, with featuure combinations
          'qty' => 1,               // product quantity in the cart
          'a_price' => 55.0,           // tax excluding price with all the option prices included
          'price_currency' => 'EUR' // 3 currency
          'article_fhd_imge_url' => 'https://eshop.domain.com/images/1920x1080_px_product_image.jpg', // optional, not mandatory url to article main image, jpg or png
          ),
          // 1 => ....  //  next   n number or products in cart
         */
        ),
        'RestoreLink' => '',
        // Link to ecommerce store, if available.
        // Link should automaticly restore customer session , 
        // With a SPECIFIC cart version.
        // Each time cart version is saved to DB, and has "_token". 
        // Module should restore the saved cart. 
        // Saved cart versions could be in DB  up to x months, configureable in module settings.
        // example : https://master.koedomain.com/?login=true&cart=fc55f988abb23685f82a8a57eca6a113a54bef2ffd73c744bbbfa27f9760b43c51d87bbfca951a10a1d6501fe96ca6329e647cc8229c17a7c4bd5f0e34321208
        'CustomerCartRestoreLink' => '',
        // Link to ecommerce store, if available.
        // Link should automaticly restore customer session , with THE LATEST cart version.
        // exapmple: https://master.DOMAIN.com/?login=true&cart=fc55f988abb23685f82a8a57eca6a113a54bef2ffd73c744bbbfa27f9760b43c51d87bbfca951a10a1d6501fe96ca6329e647cc8229c17a7c4bd5f0e34321208
        'Order' => array(// order  successfully received page. 
            'ecommerce_orderid' => 1234567, // example 1234545
            'order_date' => '', // order date, date('Y-m-d');
            'articles' => array(
                0 => array(
                    'ecommerce_article_id' => '', // magento product id, unique with product feature combinations
                    'article_title' => '', // article name 
                    'article_fhd_imge_url' => 'https://eshop.domain.com/images/1920x1080_px_product_image.jpg', // optional, not mandatory url to article main image, jpg or png
                    'qty' => '', // ordered quantity 
                    'a_price' => '', // single qty price, netto 
                    'price_currency' => '', // 3 chars, eg 'eur' ,  'usd'
                    'article_url' => '', // link to product page. not mandatory
                //example
                //    esim. 'https://master.domain.com/product/tuotteen-nimi-tarkentava-nimi/101010001/'                    
                ),
            //1 => ...         //  next, n  number of products
            )
        )
    );

    public function init() {
        if (!$this->isLoggedin())
            return;
        //echo '<pre>' . print_r( $_SERVER , true  ). '</pre>';		
        self::$tracking_data['pt_id'] = self::$public_trackingID;
        self::$tracking_data['visited_url'] = 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
        self::$tracking_data['ref_url'] = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
        self::$tracking_data['visited_from_ip'] = $_SERVER['REMOTE_ADDR'];
        self::$tracking_data['visited_from_host'] = gethostbyaddr($_SERVER['REMOTE_ADDR']);
        self::$tracking_data['web_browser'] = $_SERVER['HTTP_USER_AGENT'];

        $this->getSessionLocale();
        $this->getSessionLeadData();

        if ($this->isOrderComleteRequest()) {
            $this->getSessionOrderData();
            return;
        }

        if ($this->isProductDetailsPage()) {
            $this->getProductDetails();
        }

        $this->getCartContent();
    }

    /**
     * 	Do we have active Customer logged in session.
     *
     */
    public abstract function isLoggedin();

    /**
     *
     *
     */
    public abstract function getSessionLocale();

    public abstract function getSessionLeadData();

    /**
     *
     * 	return boolean
     */
    public abstract function isOrderComleteRequest();

    /**
     * 		Fills data for self::$tracking_data['Order'] key
     *
     */
    public abstract function getSessionOrderData();

    /**
     *
     *
     */
    public abstract function isProductDetailsPage();

    /**
     * 		Fills data for self::$tracking_data['Article'] key
     * */
    public abstract function getProductDetails();

    /**
     * 		Fills data for self::$tracking_data['Cart'] key
     * */
    public abstract function getCartContent();

    /**
     *
     * 
     * @param type $input 
     */
    protected static function utf8_encode_deep(&$input) {
        if (is_string($input)) {
            $input = utf8_encode($input);
        } else if (is_array($input)) {
            foreach ($input as &$value) {
                $this->utf8_encode_deep($value);
            }

            unset($value);
        } else if (is_object($input)) {
            $vars = array_keys(get_object_vars($input));

            foreach ($vars as $var) {
                $this->utf8_encode_deep($input->$var);
            }
        }
    }

    /**
     * Application bottom.php or "footer" callback function
     *  should be called after application output
     * @return type 
     */
    public function post_data() {

        if (!$this->isLoggedin())
            return;


        // do some validations
        if (empty(self::$tracking_data['Lead']['lead_email']))
            return;
        if (empty(self::$tracking_data['locale']))
            return;
        if (empty(self::$tracking_data['pt_id']))
            return;




        // data valid, do actual post
        $this->_post_data();
    }

    /**
     *
     *  
     */
    protected function _post_data() {

        // self::$tracking_data  must be utf8 encoded array, 
        // or json_encode returns false, 
        // if scandic letters in array values,
        // use  function if neccessary
        // self::utf8_encode_deep( self::$tracking_data  );

        $data = json_encode(self::$tracking_data);


        //prepare curl
        $cu = curl_init();
        curl_setopt($cu, CURLOPT_URL, "https://OUR.APIACCESS.ADDRESS.fi/lead_web_trackings/");
        curl_setopt($cu, CURLOPT_POST, true);
        curl_setopt($cu, CURLOPT_CONNECTTIMEOUT_MS, 1500);
        curl_setopt($cu, CURLOPT_TIMEOUT_MS, 2500);
        curl_setopt($cu, CURLOPT_POSTFIELDS, array('tdata' => $data));
        curl_setopt($cu, CURLOPT_RETURNTRANSFER, true);
        $this->responce = '';
        $crmData = curl_exec($cu);
        if (!curl_errno($cu)) {
            $info = curl_getinfo($cu);
            $this->responce = $crmData;

            //echo  '<p>Took ' . $info['total_time'] . ' seconds to send a request to ' . $info['url']  .'</p>' ;
            //if( $_SERVER['REMOTE_ADDR'] =='77.86.196.17')
            //echo '<pre>reply: ' . $crmData . '</pre>';
            //echo 'V <!-- reply: ' . $crmData . ' -->';
            //echo '<pre>' . print_r($info, true ). '</pre>';
        } else {
            //echo '<p>Curl reply error: ' . curl_error($cu) .'<p>';
        }


        curl_close($cu);
    }

}
