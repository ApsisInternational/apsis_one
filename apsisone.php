<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

class ApsisOne extends Module
{
    public function __construct()
    {
        $this->name = 'apsisone';
        $this->tab = 'advertising_marketing';
        $this->version = '1.0.0';
        $this->author = 'APSIS';
        $this->need_instance = 1;
        $this->ps_versions_compliancy = [
            'min' => '1.7',
            'max' => _PS_VERSION_
        ];
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('APSIS One Integration');
        $this->description = $this->l('Grow faster with the all-in-One marketing platform');
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
    }
}