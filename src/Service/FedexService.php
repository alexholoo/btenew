<?php

namespace Service;

use Phalcon\Di\Injectable;

use FedEx\RateService\Request;
use FedEx\RateService\ComplexType;
use FedEx\RateService\SimpleType;

const FEDEX_KEY            = 'wgfifeyAfox5ZVtA';
const FEDEX_PASSWORD       = 'YLPtFidHex14epa3Kcf6YMOA5';
const FEDEX_ACCOUNT_NUMBER = '510087380';
const FEDEX_METER_NUMBER   = '118828566';

class FedexService extends Injectable
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
        $rateRequest = new ComplexType\RateRequest();

        //authentication & client details
        $rateRequest->WebAuthenticationDetail->UserCredential->Key = FEDEX_KEY;
        $rateRequest->WebAuthenticationDetail->UserCredential->Password = FEDEX_PASSWORD;
        $rateRequest->ClientDetail->AccountNumber = FEDEX_ACCOUNT_NUMBER;
        $rateRequest->ClientDetail->MeterNumber = FEDEX_METER_NUMBER;

        $rateRequest->TransactionDetail->CustomerTransactionId = 'testing rate service request';

        //version
        $rateRequest->Version->ServiceId = 'crs';
        $rateRequest->Version->Major = 10;
        $rateRequest->Version->Minor = 0;
        $rateRequest->Version->Intermediate = 0;

        $rateRequest->ReturnTransitAndCommit = true;

        //shipper
        $rateRequest->RequestedShipment->Shipper->Address->StreetLines = ['270 Esna Park Dr.'];
        $rateRequest->RequestedShipment->Shipper->Address->City = 'Toronto';
        $rateRequest->RequestedShipment->Shipper->Address->StateOrProvinceCode = 'ON';
        $rateRequest->RequestedShipment->Shipper->Address->PostalCode = 'L3R 1H3';
        $rateRequest->RequestedShipment->Shipper->Address->CountryCode = 'CA';

        //recipient
        $rateRequest->RequestedShipment->Recipient->Address->StreetLines = [ $address ];
        $rateRequest->RequestedShipment->Recipient->Address->City = $city;
        $rateRequest->RequestedShipment->Recipient->Address->StateOrProvinceCode = $province;
        $rateRequest->RequestedShipment->Recipient->Address->PostalCode = $postalcode;
        $rateRequest->RequestedShipment->Recipient->Address->CountryCode = $country;

        //shipping charges payment
        $rateRequest->RequestedShipment->ShippingChargesPayment->PaymentType = SimpleType\PaymentType::_SENDER;
        $rateRequest->RequestedShipment->ShippingChargesPayment->Payor->AccountNumber = FEDEX_ACCOUNT_NUMBER;
        $rateRequest->RequestedShipment->ShippingChargesPayment->Payor->CountryCode = 'US';

        //rate request types
        $rateRequest->RequestedShipment->RateRequestTypes = [
            SimpleType\RateRequestType::_ACCOUNT,
            SimpleType\RateRequestType::_LIST
        ];

        $rateRequest->RequestedShipment->PackageCount = 1;

        //create package line items
        $rateRequest->RequestedShipment->RequestedPackageLineItems = [
            new ComplexType\RequestedPackageLineItem()
        ];

        //package 1
        $rateRequest->RequestedShipment->RequestedPackageLineItems[0]->Weight->Value = $weight;
        $rateRequest->RequestedShipment->RequestedPackageLineItems[0]->Weight->Units = SimpleType\WeightUnits::_LB;
        $rateRequest->RequestedShipment->RequestedPackageLineItems[0]->Dimensions->Length = $length;
        $rateRequest->RequestedShipment->RequestedPackageLineItems[0]->Dimensions->Width = $width;
        $rateRequest->RequestedShipment->RequestedPackageLineItems[0]->Dimensions->Height = $depth;
        $rateRequest->RequestedShipment->RequestedPackageLineItems[0]->Dimensions->Units = SimpleType\LinearUnits::_IN;
        $rateRequest->RequestedShipment->RequestedPackageLineItems[0]->GroupPackageCount = 1;

        $rateServiceRequest = new Request();
        $rateServiceRequest->getSoapClient()->__setLocation(Request::TESTING_URL); //use production URL PRODUCTION_URL
        $response = $rateServiceRequest->getGetRatesReply($rateRequest);

        print_r($response);

        $rate = [];

        $rate['order_id']    = $order['order_id'];
        $rate['carrier']     = 'Fedex';
        $rate['sku']         = $sku;
        $rate['ship_to']     = "$city $province $country, $postalcode";
        $rate['dimension']   = "$length x $width x $depth in";
        $rate['weight']      = "$weight lbs";
        $rate['ship_method'] = '';
        $rate['rate']        = '0.00';

        return $rate;
    }
}
