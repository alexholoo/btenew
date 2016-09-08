<?php

namespace Supplier\Synnex;

class ShipMethod
{
    static $shipMethods = [
        'CEV'    =>  'CEVA Logistics',
        'CEVT'   =>  'CEVA - TH Service',
        'CEVW'   =>  'CEVA - WH Service',
        'CPC'    =>  'Canada Post Corporation',
        'CPCE'   =>  'CANADA POST - EXPEDITED PARCEL',
        'CPCP'   =>  'CANADA POST - PRIORITY NEXT AM',
        'CPCR'   =>  'CANADA POST - REGULAR PARCEL',
        'CPCX'   =>  'CANADA POST – XPRESSPOST',
        'DARC'   =>  'Day and Ross Calgary',
        'DARG'   =>  'Day and Ross Guelph',
        'DARH'   =>  'Day and Ross Halifax',
        'DARV'   =>  'Day and Ross Vancouver',
        'DARX'   =>  'Day & Ross Small Orders',
        'DSV'    =>  'Drop Ship Vendor',
        'EDEL'   =>  'Electronic Delivery (email)',
        'FDXH'   =>  'FedEx Express',
        'FWC'    =>  'Forwarded Will Call',
        'KNH'    =>  'K&H Dispatch',
        'KNHS'   =>  'K&H Special',
        'OXR'    =>  'Onward Express Rush',
        'P10X'   =>  'Purolator Express 10:30am',
        'P9X'    =>  'Purolator Express 9:00am',
        'PSA'    =>  'Purolator Saturday',
        'PUA'    =>  'Purolator Express Air',
        'PUG'    =>  'Purolator Ground',
        'PUX'    =>  'Purolator Express Ground',
        'QR2'    =>  'Quick-Run Overnight',
        'ROD1'   =>  'Routes Display Dist 1 pallet',
        'ROD2'   =>  'Routes Display Dist 2 pallet',
        'ROD3'   =>  'Routes Display Dist 3 pallet',
        'RODQ'   =>  'Routes Distribution Quarters',
        'ROU'    =>  'Routes LTL',
        'ROUI'   =>  'ROUTES INBOUND',
        'ROUQ'   =>  'Routes quarters guelph',
        'ROUS'   =>  'Routes Display Special',
        'ROUT'   =>  'Routes Truck Load',
        'S40H'   =>  'SCHENKER 40 FT HIGH CUBE CONT',
        'SAIR'   =>  'SCHENKER AIRFREIGHT',
        'SC20'   =>  'Schenker 20\' Container',
        'SC40'   =>  'Schenker 40\' Container',
        'SC45'   =>  'Schenker 45\' Container',
        'SC4H'   =>  'Schenker 40\' High Cube Contnr',
        'SDL'    =>  'Dynamex Exp - Same Day Local',
        'SDS'    =>  'Dynamex Exp - Same Day Direct',
        'SDT'    =>  'Sameday Truck',
        'SFTL'   =>  'SCHENKER FULL TRUCKLOAD',
        'SLCL'   =>  'SCHENKER LESS THAN CONTAINER',
        'SLTL'   =>  'TRUCKLOAD',
        'ST2'    =>  'Strait Consolidate Stx',
        'STX'    =>  'Strait Express',
        'UPG'    =>  'UPS Standard',
        'UPGC'   =>  'UPS Ground for Collect',
        'UPSS'   =>  'UPS Express Saver',
        'UPX'    =>  'UPS Express Saver',
        'WC'     =>  'Customer Pick-Up',
        'WCET'   =>  'Pick-Up in Etobicoke',
        'WCGU'   =>  'Pick-Up Guelph',
        'WCMI'   =>  'Customer Pick-Up',
        'WCRO'   =>  'Logitec Display Shipment',
        'WHS'    =>  'Warehouse Select',
    ];

    public static function getName($code)
    {
        $name = '';

        if (isset(self::$shipMethods[$code])) {
            $name = self::$shipMethods[$code];
        }

        return $name;
    }

    public static function all()
    {
        return self::$shipMethods;
    }
}
