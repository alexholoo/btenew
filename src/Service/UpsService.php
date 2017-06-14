<?php

namespace Service;

use Phalcon\Di\Injectable;

class UpsService extends Injectable
{
    public function getRate($order)
    {
        $address    = $order['address']['address'];
        $city       = $order['address']['city'];
        $province   = $order['address']['province'];
        $country    = $order['address']['country'];
        $postalcode = $order['address']['postalcode'];

        $sku    = $order['items'][0]['sku'];
        $info   = $this->skuService->getMasterSku($sku);

        $length = $info['Length'];
        $width  = $info['Width'];
        $depth  = $info['Depth'];
        $weight = $info['Weight'];

        //
        $accessKey = '';
        $userId = '';
        $password = '';

        $rate = new \Ups\Rate($accessKey, $userId, $password);

        try {
            $shipment = new \Ups\Entity\Shipment();

            $shipperAddress = $shipment->getShipper()->getAddress();
            $shipperAddress->setPostalCode('L3R 1H3');

            $address = new \Ups\Entity\Address();
            $address->setPostalCode('L3R 1H3');
            $shipFrom = new \Ups\Entity\ShipFrom();
            $shipFrom->setAddress($address);

            $shipment->setShipFrom($shipFrom);

            $shipTo = $shipment->getShipTo();
            $shipTo->setCompanyName('BTE Customer');
            $shipToAddress = $shipTo->getAddress();
            $shipToAddress->setPostalCode($postalcode);

            $package = new \Ups\Entity\Package();
            $package->getPackagingType()->setCode(\Ups\Entity\PackagingType::PT_PACKAGE);
            $package->getPackageWeight()->setWeight($weight);

            // if you need this (depends of the shipper country)
            $weightUnit = new \Ups\Entity\UnitOfMeasurement;
            $weightUnit->setCode(\Ups\Entity\UnitOfMeasurement::UOM_KGS);
            $package->getPackageWeight()->setUnitOfMeasurement($weightUnit);

            $dimensions = new \Ups\Entity\Dimensions();
            $dimensions->setHeight($depth);
            $dimensions->setWidth($width);
            $dimensions->setLength($length);

            $unit = new \Ups\Entity\UnitOfMeasurement;
            $unit->setCode(\Ups\Entity\UnitOfMeasurement::UOM_IN);

            $dimensions->setUnitOfMeasurement($unit);
            $package->setDimensions($dimensions);

            $shipment->addPackage($package);

            print_r($rate->getRate($shipment));
        } catch (Exception $e) {
            print_r($e);
        }

        $rate = [];

        $rate['order_id']    = $order['order_id'];
        $rate['carrier']     = 'UPS';
        $rate['sku']         = $sku;
        $rate['ship_to']     = "$city $province $country, $postalcode";
        $rate['dimension']   = "$length x $width x $depth in";
        $rate['weight']      = "$weight lbs";
        $rate['ship_method'] = '';
        $rate['rate']        = '0.00';

        return $rate;
    }

    protected function getServiceCode($service = 'GND')
    {
        $defaultServiceCode = '03';

        $map = [
            '1DM'    => '14',
            '1DA'    => '01',
            '1DAPI'  => '01',
            '1DP'    => '13',
            '2DM'    => '59',
            '2DA'    => '02',
            '3DS'    => '12',
            'GND'    => '03',
            'GNDRES' => '03',
            'GNDCOM' => '03',
            'STD'    => '11',
            'XPR'    => '07',
            'XDM'    => '54',
            'XPD'    => '08',
        ];

        $service = strtoupper($service);

        return isset($map[$service]) ? $map[$service] : $defaultServiceCode;
    }
}
