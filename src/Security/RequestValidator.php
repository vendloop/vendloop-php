<?php

namespace Vendloop\Security;

/**
 * RequestValidator is a helper to validate that a request to a web server was actually made from Vendloop
 * EXAMPLE USAGE:
 * $validator = new RequestValidator('SECRET_KEY');
 * $isFromVendloop = $validator->validate($_SERVER['HTTP_X_VENDLOOP_SIGNATURE'], 'https://your-example-url.com/api/route/', $_REQUEST);
 * $isFromVendloop // <- if this is true, the request did come from Vendloop, if not, it didn't
 */
class RequestValidator {

    /**
     * @access private
     * @var string The api key to the Vendloop Account
     */
    private $api_key;
	
    /**
     * @access protected
     * @var string The server http header from vendloop
     */
    protected $signature = '';

    /**
     * constructor
     * @access public
     * @param string $api_key the api key to the Vendloop Account
     * Sets the account api key to be used by the rest of the class
     */
    public function __construct(string $api_key) {
        $this->api_key = $api_key;
        $this->signature = (isset($_SERVER['HTTP_X_VENDLOOP_SIGNATURE']) ? $_SERVER['HTTP_X_VENDLOOP_SIGNATURE'] : '' );
    }
	
    /**
     * The only method the client should be running...takes the data and validates the signature
     *
     * @param array|string $data
     * @return bool
     */
	public function validate(string $data): bool {
        if ($this->signature && $data) {
			$hash = hash_hmac('sha512', $data, $this->api_key);
            return hash_equals($this->signature, $hash);
        }
        return false;
	}
	
}