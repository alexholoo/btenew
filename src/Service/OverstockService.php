<?php

namespace Service;

use Phalcon\Di\Injectable;

class OverstockService extends Injectable
{
    public function isOverstocked($sku)
    {
        $result = $this->db->fetchOne("SELECT * FROM overstock WHERE sku='$sku'");
        return (boolean)$result;
    }

    /**
     * for restore
     */
    public function import($filename)
    {
        if (!($fp = @fopen($filename, 'rb'))) {
            // "Failed to open file: $filename\n";
            return;
        }

        fgetcsv($fp); // skip the first line

        $this->db->execute('TRUNCATE TABLE overstock');

        $columns = $this->getColumns();

        $count = 0;

        while (($fields = fgetcsv($fp))) {

            $sku        = $fields[0];
            $title      = $fields[1];
            $cost       = $fields[2];
            $condition  = $fields[3];
            $allocation = $fields[4];
            $qty        = $fields[5];
            $mpn        = $fields[6];
            $note       = $fields[7];
            $upc        = $fields[8];
            $weight     = $fields[9];
            $reserved   = $fields[10];
            $row_num    = $fields[11];

            try {
                $this->db->insertAsDict('overstock', [
                    'sku'        => $sku,
                    'title'      => $title,
                    'cost'       => $cost,
                    'condition'  => $condition,
                    'allocation' => $allocation,
                    'qty'        => $qty,
                    'mpn'        => $mpn,
                    'note'       => $note,
                    'upc'        => $upc,
                    'weight'     => $weight,
                    'reserved'   => $reserved,
                    'row_num'    => $row_num,
                ]);

                $count++;

            } catch (Exception $e) {
                // echo $e->getMessage(), EOL;
            }
        }

        fclose($fp);

        return $count;
    }

    /**
     * for backup
     */
    public function export($filename)
    {
        if (!($fp = @fopen($filename, 'w'))) {
            // "Failed to create file: $filename\n";
            return;
        }

        fputcsv($fp, $this->getColumns());

        $result = $this->db->fetchAll('SELECT * FROM overstock');

        foreach($result as $item) {
            fputcsv($fp, [
                $row['sku'],
                $row['title'],
                $row['cost'],
                $row['condition'],
                $row['allocation'],
                $row['qty'],
                $row['mpn'],
                $row['note'],
                $row['upc'],
                $row['weight'],
                $row['reserved'],
                $row['row_num'],
            ]);
        }

        fclose($fp);
    }

    protected function getColumns()
    {
        return [
            'sku',
            'title',
            'cost',
            'condition',
            'allocation',
            'qty',
            'mpn',
            'note',
            'upc',
            'weight',
            'reserved',
            'row_num',
        ];
    }
}
