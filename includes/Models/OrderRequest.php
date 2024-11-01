<?php

namespace Simpler\Models;

final class OrderRequest
{
    /**
     * @var User
     */
    private $user;
    /**
     * @var Order
     */
    private $order;
    /**
     * @var Address
     */
    private $shipTo;
    /**
     * @var InvoiceDetails | null
     */
    private $invoiceDetails;

    public function __construct(User $user, Order $order, Address $shipTo = null, InvoiceDetails $invoiceDetails = null)
    {
        $this->user   = $user;
        $this->order  = $order;
        $this->shipTo = $shipTo;
        $this->invoiceDetails = $invoiceDetails;
    }

    public function get_user()
    {
        return $this->user;
    }

    public function get_order()
    {
        return $this->order;
    }

    public function get_ship_to()
    {
        return $this->shipTo ?? false;
    }

    public function is_invoice()
    {
        return !is_null($this->invoiceDetails);
    }

    public function get_invoice_details(): InvoiceDetails
    {
        return $this->invoiceDetails;
    }
}
