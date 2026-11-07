<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

/**
 * Panelden yazdırılan sipariş fişi (paketleme ve teslimat için).
 * Yalnız panel kullanıcıları görebilir: müşteri adresi ve telefonu içerir.
 */
class SiparisFisiController extends Controller
{
    public function __invoke(Request $request, Order $order)
    {
        abort_unless($request->user()?->panelde(), 403);

        return view('panel.siparis-fisi', ['order' => $order->load('items')]);
    }
}
