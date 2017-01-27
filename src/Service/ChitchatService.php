<?php

namespace Service;

use Phalcon\Di\Injectable;

class ChitchatService extends Injectable
{
    public function list()
    {
        $sql = "SELECT OrderNumber, Carrier, TrackingNum, ShipDate, 'shippingeasy' as Source
                  FROM chitchat cc
                  LEFT JOIN shippingeasy se ON se.TrackingNumber = cc.trackingnum
                  ORDER BY seqno DESC";

        $info = $this->db->fetchAll($sql);

        return $info;
    }

    public function save($tracking)
    {
        if (strlen(trim($tracking)) == 0) {
            return 0;
        }

		if (strlen($tracking) > 22) {
            // truncate first few numbers due to scanning issue
			$tracking = substr($tracking, -22);
		}

        try {
            $this->db->insertAsDict('chitchat', [
                'trackingnum' => trim($tracking),
            ]);
            return 1;
        } catch (\Exception $e) {
            // echo $e->getMessage(), EOL;
        }
        return 0;
    }

    public function export()
    {
        $filename = "w:/out/chitchat.csv";
        $filename = "e:/chitchat.csv";

        if (($fp = fopen($filename, 'w+')) == false) {
            return;
        }

        $csvTitle = [
            'Date',
            'Class',
            'Recipient Name & Address',
            'Item',
            'lbs',
            'Value',
            'Handling charge',
            'Tracking',
        ];
        fputcsv($fp, $csvTitle);

        $sql = "SELECT cc.*, se.*
                  FROM chitchat cc
                  LEFT JOIN shippingeasy se ON se.TrackingNumber = cc.TrackingNum";

        $items = $this->db->fetchAll($sql);
        foreach ($items as $item) {

           #$class    = '1st Class';
           #$handling = '$0.65';

            $date     = $item['ShipDate'];
            $address  = $item['Recipient'].', '.$item['RecipientShippingAddress'];
            $total    = $item['OrderTotal'];
            $tracking = $item['trackingnum'];
            $weight   = $item['WeightOZ']/16;

            if ($weight < 1) {
                $class = '1st Class';
                $handling = '$0.65';
            }
            else if ($weight >= 1) {
                $class = 'Priority';
                $handling = '$1.00';
            }

           #$itemName = 'PC Parts';
            $itemName = $this->getItemDesc($item['ItemName']);

            fputcsv($fp, [
                $date,
                $class,
                $address,
                $itemName,
                $weight,
                $total,
                $handling,
                $tracking,
            ]);
        }

        fclose($fp);
    }

    public function delete($items)
    {
        if (empty($items)) {
            return;
        }

        foreach ($items as $tracking) {
            $this->db->execute("DELETE FROM chitchat WHERE trackingnum='$tracking'");
        }
    }

    public function deleteAll()
    {
        $this->db->execute("TRUNCATE TABLE chitchat");
    }

    protected function getItemDesc($itemName)
    {
        $map = [
            'router'           => 'Router',
            'speaker'          => 'Speaker',
            'motherboard'      => 'Motherboard',
            'graphics card'    => 'Graphics Card',
            'webcam'           => 'Webcam',
            'dimm'             => 'RAM',
            'cable'            => 'Cable',
            'adapter'          => 'Adapter',
            'headset'          => 'Headset',
            'toner'            => 'Toner',
            'software'         => 'Software',
            'case'             => 'Case',
            'headphone'        => 'Headphones',
            'ssd'              => 'SSD',
            'hdd'              => 'HDD',
            'keyboard'         => 'Keyboard',
            'mp3'              => 'MP3 Player',
            'sdhc'             => 'Memory Card',
            'sdhc'             => 'Memory Card',
            'stylus'           => 'Stylus',
            'screen protector' => 'Screen protector',
        ];

        foreach ($map as $key => $val) {
            if (stripos($itemName, $key) !== false) {
                return $val;
            }
        }

        return str_replace("'", "''", $itemName);
    }
}
