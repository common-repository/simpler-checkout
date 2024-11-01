<?php

namespace Simpler\Models;

final class InvoiceDetails
{
    private $tax_id;
    private $company_name;
    private $company_activity;
    private $company_address;
    private $tax_authority;

    public function __construct($tax_id, $company_name, $company_activity, $company_address, $tax_authority)
    {
        $this->tax_id = $tax_id;
        $this->company_name = $company_name;
        $this->company_activity = $company_activity;
        $this->company_address = $company_address;
        $this->tax_authority = $tax_authority;
    }

    public static function from_json(array $json)
    {
        return new InvoiceDetails(
            $json['tax_id'],
            $json['company_name'],
            $json['activity'],
            $json['company_address'],
            $json['tax_authority']
        );
    }

    public function get_tax_id()
    {
        return $this->tax_id;
    }

    public function get_company_name()
    {
        return $this->company_name;
    }

    public function get_company_activity()
    {
        return $this->company_activity;
    }

    public function get_company_address()
    {
        return $this->company_address;
    }

    public function get_tax_authority()
    {
        return $this->tax_authority;
    }
}
