<?php

require_once COREPATH . 'core/components/vendor/autoload.php';
use QuickBooksOnline\API\DataService\DataService;

//session_start();

class QB_ONLINE {

    function __construct() {
        if( !isset( $_SESSION['qb_token'] ) ) {
            global $cbuss;
            if( isset( $cbuss['id'] ) ) {
                $config = include('config.php');
                $keys = get_options(['qb_client_id', 'qb_client_secret'], $cbuss['id']);
                $config['client_id'] = $keys['qb_client_id'];
                $config['client_secret'] = $keys['qb_client_secret'];
                //skel($config);
                // Create SDK instance
                $dataService = DataService::Configure(array(
                    'auth_mode' => 'oauth2',
                    'ClientID' => $config['client_id'],
                    'ClientSecret' => $config['client_secret'],
                    'RedirectURI' => $config['oauth_redirect_uri'],
                    'scope' => $config['oauth_scope'],
                    'baseUrl' => "development"
                ));
                $OAuth2LoginHelper = $dataService->getOAuth2LoginHelper();
                $authUrl = $OAuth2LoginHelper->getAuthorizationCodeURL();
                skel($authUrl);

                skel( $_SERVER['QUERY_STRING'] );
                $parseUrl = $this->parseAuthRedirectUrl( $_SERVER['QUERY_STRING'] );
                skel( $parseUrl );

                /*$accessToken = $OAuth2LoginHelper->exchangeAuthorizationCodeForToken($parseUrl['code'], $parseUrl['realmId']);
                $dataService->updateOAuth2Token($accessToken);

                $_SESSION['qb_token'] = $accessToken;*/
            }
        }
    }

    function parseAuthRedirectUrl( $url ) {
        parse_str( $url, $qsArray );
        return array(
            'code' => isset( $qsArray['code'] ) ? $qsArray['code'] : '',
            'realmId' => isset( $qsArray['realmId'] ) ? $qsArray['realmId'] : ''
        );
    }

    public function get_customers() {
        return get_business_clients();
    }

    function get_customer( $id ) {

    }

    function add_customer() {

    }

    function update_customer() {

    }

    function get_suppliers() {

    }

    function get_supplier( $id ) {

    }

    function add_supplier() {

    }

    function update_supplier() {

    }

    function get_products() {

    }

    function get_product() {

    }

    function add_product() {

    }

    function get_services() {

    }

    function get_service() {

    }

    function add_service() {

    }

    function get_invoices() {

    }

    function get_invoice() {

    }

    function add_sale_invoice() {

    }
}