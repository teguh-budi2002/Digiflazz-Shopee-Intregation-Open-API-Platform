<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;

class DigiflazzService {

  protected static $endpoint = 'https://api.digiflazz.com/v1';

  public static function checkBalance() {
    $payload = [
      'cmd' => 'deposit',
      'username' => config('digiflazz.DIGIFLAZZ_USERNAME'),
      'sign' => self::getSign("depo")
    ];
    $resp = self::requestAPI("/cek-saldo", $payload);
    if ($resp->failed()) {
      return json_decode($resp);
    }
    return $resp->json('data');
  }

  public static function getPriceList() {
    $payload = [
      'cmd' => 'prepaid',
      'username' => config('digiflazz.DIGIFLAZZ_USERNAME'),
      'sign' => self::getSign("depo")
    ];
    $resp = self::requestAPI("/price-list", $payload);

    if ($resp->failed()) {
      return json_decode($resp);
    }
    return $resp->json();
  }

  public static function purchase() {
    $ref_id = self::generateRefId();
    $payload = [
      'username' => config('digiflazz.DIGIFLAZZ_USERNAME'),
      'buyer_sku_code' => 'xld10', // mode testing
      'customer_no' => '087800001230', // mode testing
      'ref_id' => $ref_id,
      'sign' => self::getSign($ref_id),
      'testing' => true,
    ];
    $resp = self::requestAPI("/transaction", $payload);

    if ($resp->failed()) {
      return json_decode($resp);
    }
    return $resp->json('data');
  }

  public static function getSign($request) {
    $sign = md5(config('digiflazz.DIGIFLAZZ_USERNAME') . self::getKey() . $request);
    return $sign;
  }

  public static function getKey() {
    if (config('digiflazz.isProd') === 'local') {
      return config('digiflazz.DIGIFLAZZ_DEV_KEY');
    } else {
      return config('digiflazz.DIGIFLAZZ_PROD_KEY');
    }
  }

  public static function requestAPI($subUrl, $payload){
    return Http::retry(3, 100)
                ->accept('application/json')
                ->post(self::$endpoint . $subUrl, $payload);
  }

  public static function generateRefId(){
    $prefix = "TRX-";
    return $prefix . uniqid();
  }
}