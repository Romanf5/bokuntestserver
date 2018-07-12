<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\RequestLog;

class CURLController extends Controller
{
    private $url = 'https://api.bokun.is';
    private $accessKey = '24f2bb7ce61f488ba6033c5c9eb16a84';
    private $secretKey = 'badfde9ec56b47d8aa6a53b932b7fdaf';
    private $algoritm = 'sha1';
    private $slug;
    private $method;
    private $date;
    private $ch;

    public function __construct() {
      $this->date = Carbon::now()->format('Y-m-d H:i:s');
      $this->ch = curl_init();
    }

    public function SendRequest(Request $request) {
      $this->method = $request->method();
      $this->slug = $request->slug;
      $headers = array(
        'Content-Type: application/json',
        'X-Bokun-AccessKey: ' . $this->accessKey,
        'X-Bokun-Date: ' . $this->date,
        'X-Bokun-Signature: ' . $this->hashGen()
      );
      curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($this->ch, CURLOPT_URL, $this->url . $this->slug);
      curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, $this->method);
      curl_setopt($this->ch, CURLOPT_HTTPHEADER, $headers);
      $output = curl_exec($this->ch);
      curl_close($this->ch);
      $this->logRequest($output);
      return $output;
    }

    private function hashGen() {
      $str = $this->date . $this->accessKey . $this->method . $this->slug;
      return base64_encode(hash_hmac($this->algoritm, $str, $this->secretKey, true));
    }

    private function logRequest($response) {
      $log = [
        'method' => $this->method,
        'slug' => $this->slug,
        'response' => $response
      ];
      RequestLog::create($log);
    }
}
