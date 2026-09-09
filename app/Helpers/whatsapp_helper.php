<?php

namespace App\Helpers;

use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\User;
use App\Models\WhatsappMessage;
use Illuminate\Support\Facades\URL;

class whatsapp_helper
{
    public static function whatsapp_message_config($vendor_id)
    {
        return WhatsappMessage::where('vendor_id', $vendor_id)->first();
    }

    public static function whatsappmessage($order_number, $vdata, $vendordata)
    {
        
    }

    public static function orderstatusupdatemessage($order_number, $status, $vendor_id)
    {
        
    }
}
