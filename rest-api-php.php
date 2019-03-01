<?php

class RESTbitsgap
{
    protected $publicKey = '';
    protected $privateKey = '';
    protected $base_url  = 'https://bitsgap.com/api/';
    protected $api_version = 'v1';
    protected $url = '';

    public function __construct($options = null){
        $this->url = $this->base_url . $this->api_version;
    }

    protected function getData($data = null){
	    $dataArr = [
        'key'   =>  $this->publicKey
      ];
      if (isset($data)) {
        $dataArr = array_merge($dataArr, $data);
      }
      $data_str = json_encode($dataArr);
	    $sendData = [
        'key'   	=>  $this->publicKey,
        'data' 		=>  $data_str,
        'signature'	=>  $this->getSign($data_str)
      ];
      return $sendData;
    }

    protected function getSign($data_str) {
		    return hash_hmac('SHA512', $data_str, $this->privateKey);
    }

    protected function httpRequest($url, $data = null){
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($curl,CURLOPT_POST,1);
        curl_setopt($curl,CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        curl_close($curl);
        return $output;
    }

    public function request($name, $params=null) {
	     $sendData  = $this->getData($params);
       $url = $this->url.'/'.$name;
       return $this->httpRequest($url, $sendData);
    }

    /* Get markets and pairs list*/
    public function markets() {
      return $this->request('markets');
    }

    /* Get markets and pairs last price
      Request pamars:
        market - market name
        pair - bitsgap pair name
    */
    public function last_price($market, $pair) {
      $params = [
        'market'  => $market,
        'pair'    => $pair
      ];
      return $this->request('last-price', $params);
    }

    /* Get markets and pairs orderbook
      Request pamars:
        market - market name
        pair - bitsgap pair name
    */
    public function orderbook($market, $pair) {
      $params = [
        'market'  => $market,
        'pair'    => $pair
      ];
      return $this->request('order-book', $params);
    }

    /* Get markets and pairs ticker orderbook
      Request pamars:
        market - market name
        pair - bitsgap pair name
        start - begin timestamp interval
        end - end timestamp interval
    */
    public function tick_orderbook($market, $pair, $start, $end) {
      $params = [
        'market'  => $market,
        'pair'    => $pair,
        'start'   => $start,
        'end'     => $end,
      ];
      return $this->request('ticker/order-book', $params);
    }

    /* Get markets and pairs ticker transactions
      Request pamars:
        market - market name
        pair - bitsgap pair name
        start - begin timestamp interval
        end - end timestamp interval
    */
    public function tick_transactions($market, $pair, $start, $end) {
      $params = [
        'market'  => $market,
        'pair'    => $pair,
        'start'   => $start,
        'end'     => $end,
      ];
      return $this->request('ticker/transactions', $params);
    }

    /* Get markets and pairs Open-High-Low-Close values
      Request pamars:
        market - market name
        pair - bitsgap pair name
        start - begin timestamp interval
        end - end timestamp interval
    */
    public function ohlc($market, $pair, $start, $end) {
      $params = [
        'market'  => $market,
        'pair'    => $pair,
        'start'   => $start,
        'end'     => $end,
      ];
      return $this->request('ohlc', $params);
    }

    /* Get markets and pairs recent trades
      Request pamars:
        market - market name
        pair - bitsgap pair name
    */
    public function recent_trades($market, $pair) {
      $params = [
        'market'  => $market,
        'pair'    => $pair
      ];
      return $this->request('recent-trades', $params);
    }

    // Trade methods

    /*
     * Get balances
     */
    public function balances($is_demo=false) {
      $request_path = 'balance';
      if($is_demo) {
        $request_path = 'demo/'.$request_path;
      }
      return $this->request($request_path);
    }

    /*
     * Get open orders
     * Request pamars:
     *   market - market name
     */
    public function orders_open($market, $is_demo=false) {
      $request_path = 'orders/open';
      $params = [
        'market'  => $market
      ];
      if($is_demo) {
        $request_path = 'demo/'.$request_path;
      }
      return $this->request($request_path, $params);
    }

    /*
     * Get closed orders
     * Request pamars:
     *   market - market name
     */
    public function orders_history($market, $is_demo=false) {
      $params = [
        'market'  => $market
      ];
      $request_path = 'orders/history';
      if($is_demo) {
        $request_path = 'demo/'.$request_path;
      }
      return $this->request($request_path, $params);
    }

    /*
     * Place order
     * Request pamars:
     *   market - market name
     *   pair - bitsgap pair name
         amount - order amount
         side - order side "buy" or "sell"
         type - order type "limit", "market"
         price - order price for limit or shadow orders
     */
    public function order_place($market, $pair, $amount, $side, $type='limit', $price=null, $is_demo=false) {
      $params = [
        'market'  => $market,
        'pair'    => $pair,
        'amount'  => $amount,
        'side'    => $side,
        'type'    => $type,
      ];

      if (isset($price)) {
        $params['price'] = $price;
      }

      $request_path = 'orders/add';
      if($is_demo) {
        $request_path = 'demo/'.$request_path;
      }
      return $this->request($request_path, $params);
    }

    /*
     * Cancel order
     * Request pamars:
     *   market - market name
     *   pid - market order id
     */
    public function order_cancel($market, $pid, $is_demo=false) {
      $params = [
        'market'  => $market,
        'pid'     => $pid
      ];
      $request_path = 'orders/cancel';
      if($is_demo) {
        $request_path = 'demo/'.$request_path;
      }
      return $this->request($request_path, $params);
    }
}

// examples
$rest_api = new RESTbitsgap();

/* Market methods */

// test markets
print_r($rest_api->markets());

// test last-price
//print_r($rest_api->last_price('kraken', 'BTC_USD'));

// test market and pair orderbook
//print_r($rest_api->orderbook('kraken', 'BTC_USD'));

// test market and pair ticker orderbook in selected interval
//print_r($rest_api-> tick_orderbook('kraken', 'BTC_USD', time() - 3600, time()));

// test market and pair ticker transactions in selected interval
//print_r($rest_api->tick_transactions('kraken', 'BTC_USD', time() - 3600, time()));

// test market and pair OHLC in selected interval
//print_r($rest_api->ohlc('kraken', 'BTC_USD', time() - 3600, time()));

// test market and pair Recent Trades
//print_r($rest_api->recent_trades('kraken', 'BTC_USD'));

/* User methods */

// user balance real markets
print_r($rest_api->balances());
// user balance demo markets
print_r($rest_api->balances(true));

// user open orders real markets
//print_r($rest_api->orders_open('idex'));
// user open orders demo markets
//print_r($rest_api->orders_open('kraken', true));

// user closed orders real markets
//print_r($rest_api->orders_history('kraken'));
// user closed orders demo markets
//print_r($rest_api->orders_history('kraken', true));

// user place order real markets
//print_r($rest_api->order_place('bittrex', 'EDR_USD', '150', 'sell', 'limit', '0.2'));
// user place order demo markets
//print_r($rest_api->order_place('kraken', 'ADA_USD', '10', 'buy', 'limit', '0.04', true));
//print_r($rest_api->order_place('kraken', 'ADA_USD', '10', 'buy', 'market', null, true));

// user cancel order real market
//print_r($rest_api->order_cancel('bit-z', '16xx68xx1x1'));
// user cancel order demo market
//print_r($rest_api->order_cancel('kraken', '5fxxxxxxxxxxxxxxxxxxxxxxxxxx32', true));
