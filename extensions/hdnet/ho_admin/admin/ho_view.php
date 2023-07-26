<?php

namespace hdnet\ho_admin\admin;
use oxAdminDetails;

/**
 * Billpay view.
 * Displays all Billpay-related information for an order.
 * Also allows Billpay-dependent administrative actions like order activation.
 * Admin Menu: Orders -> Display Orders -> Billpay.
 * @package admin
 */
class ho_View extends oxAdminDetails
{

    public function render()
    {
        parent::render();

    }

return "billpay_view.tpl";
}

}