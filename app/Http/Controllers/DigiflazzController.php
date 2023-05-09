<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\DigiflazzService;

class DigiflazzController extends Controller
{
    public function test() {
      // $repo = DigiflazzRepository::checkBalance();
      // $repo = DigiflazzRepository::getPriceList();
      $repo = DigiflazzService::purchase();
      // $repo = DigiflazzRepository::requestAPI("/cek-saldo", "payload");
      dd($repo);
    }
}
