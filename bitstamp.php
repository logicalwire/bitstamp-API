<?php
//THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
//Activate the sound alert by creating a folder with name "sound" in the same folder that the php file "alerts.php" and "bitstamp.php" and put 2 wav file one named "alertmore.wav" and another named "alertless.wave".
//please send BTC at: 1A42umF893jp3wf94juRhGArFQrqkdxnnM

class Bitstamp {
    private $_key;
    private $_secret;
    private $_clientId;
    public function __construct($key, $secret, $clientId) {
        $this->_key = $key;
        $this->_secret = $secret;
        $this->_clientId = $clientId;
    }
    public function ticker() {
        return $this->_doGET('ticker', array(), 'post');
    }
    public function orderBook($unified = 1) {
        return $this->_doGET('order_book', array('group' => $unified));
    }
    public function transactions($offset = 0, $limit = 100, $sort = 'desc') {
        $sort = strtolower($sort)=='asc'?'asc':'desc';
        return $this->_doGET('transactions', array('offset' => $offset, 'limit' => $limit, 'sort' => $sort));
    }
    public function rate() {
        return $this->_doGET('eur_usd');
    }
    public function balance() {
        return $this->_doRequest('balance');
    }
    public function userTransactions($offset = 0, $limit = 100, $sort = 'desc') {
        $sort = strtolower($sort)=='asc'?'asc':'desc';
        return $this->_doRequest('user_transactions', array('offset' => $offset, 'limit' => $limit, 'sort' => $sort));
    }
    public function openOrders() {
        return $this->_doRequest('open_orders');
    }
    public function cancelOrder($id) {
        return $this->_doRequest('cancel_order', array('id' => $id));
    }
    public function buy($amount, $price) {
        return $this->_doRequest('buy', array('amount' => $amount, 'price' => $price));
    }
    public function sell($amount, $price) {
        return $this->_doRequest('sell', array('amount' => $amount, 'price' => $price));
    }
    public function checkCode($code) {
        return $this->_doRequest('check_code', array('code' => $code));
    }
	public function redeemCode($code) {
        return $this->_doRequest('redeem', array('code' => $code));
    }
	public function withdrawals() {
        return $this->_doRequest('withdrawal_request');
    }
    public function withdrawalBitcoin($amount, $address) {
        return $this->_doRequest('bitcoin_withdrawal', array('amount' => $amount, 'address' => $address));
    }
    public function depositBitcoin() {
        return $this->_doRequest('bitcoin_deposit_address');
    }
    public function unconfirmedDeposits() {
        return $this->_doRequest('unconfirmed_btc');
    }
    public function withdrawalRipple($amount, $address, $currency) {
        return $this->_doRequest('ripple_withdrawal', array('amount' => $amount, 'address' => $address, 'currency' => $currency));
    }
    public function depositRipple() {
        return $this->_doRequest('ripple_address');
    }
    private function _doRequest($action, array $params = array(), $method = 'post') {
        $time = explode(" ", microtime());
        $nonce = $time[1].substr($time[0], 2, 6);
        $params['nonce'] = $nonce;
        $params['key'] = $this->_key;
        $params['signature'] = $this->_signature($nonce);
        $request = http_build_query($params, '', '&');
        $headers = array();
        static $curl = null;
        if(is_null($curl)) {
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        }
        curl_setopt($curl, CURLOPT_URL, 'https://www.bitstamp.net/api/'.$action.'/');
        if($method=='POST') { curl_setopt($curl, CURLOPT_POST, false); }
        curl_setopt($curl, CURLOPT_POSTFIELDS, $request); //POST ERROR   
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($curl,CURLOPT_SSL_VERIFYPEER, false);
        $json = curl_exec($curl);
        if($json === false) {
            return array('error' => 1, 'message' => curl_error($curl));
        }
        $data = json_decode($json, true);
        if(!$data) {
            return array('error' => 2, 'message' => 'Invalid data received, please make sure connection is working and requested API exists');
        }
        return $data;
    }
	    private function _doGET($action, array $params = array(), $method = 'post') {
        $time = explode(" ", microtime());
        $nonce = $time[1].substr($time[0], 2, 6);
        $params['nonce'] = $nonce;
        $params['key'] = $this->_key;
        $params['signature'] = $this->_signature($nonce);
        $request = http_build_query($params, '', '&');

        $headers = array();
        static $curl = null;
        if(is_null($curl)) {
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        }
        curl_setopt($curl, CURLOPT_URL, 'https://www.bitstamp.net/api/'.$action.'/');
        if($method=='POST') { curl_setopt($curl, CURLOPT_POST, false); }  
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($curl,CURLOPT_SSL_VERIFYPEER, false);
        $json = curl_exec($curl);
        if($json === false) {
            return array('error' => 1, 'message' => curl_error($curl));
        }
        $data = json_decode($json, true);
        if(!$data) {
            return array('error' => 2, 'message' => 'Invalid doGET method');
        }
        return $data;
    }
    private function _signature($nonce) {
        return strtoupper(hash_hmac('sha256', ($nonce.$this->_clientId.$this->_key), $this->_secret));

    }
}
