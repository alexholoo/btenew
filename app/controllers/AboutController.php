<?php
namespace App\Controllers;

use Supplier\Supplier;
use Supplier\DH\Client       as DHClient;
use Supplier\Synnex\Client   as SynnexClient;
use Supplier\Ingram\Client   as IngramClient;
use Supplier\Techdata\Client as TechdataClient;

/**
 * Display the "About" page.
 */
class AboutController extends ControllerBase
{
    /**
     * Default action. Set the public layout (layouts/public.volt)
     */
    public function indexAction()
    {
        //$this->view->data = print_r($this->config->toArray(), true);
        //$this->view->data = $this->request->getServerAddress();
        $this->view->data = gethostname();

        //$config = $this->config->toArray();
        //fpr($config['xmlapi']['synnex']);
        //
        //$config = $this->config->xmlapi->toArray();
        //fpr($config['synnex']);
#
        //$amazonService = $this->amazonService;
        //$amazonService->doSomething();
#
        //$client = new SynnexClient();
        //$client->getPriceAvailability();
        //fpr($req->toXml());
#
        // why is so slow?
        //$this->queue->put(array(
        //    'TestJob' => __METHOD__ . '-' . rand(),
        //));
#
        #$this->queue->put(array(
        #    'PriceAvailJob' => 0,
        #));
#
/*
        $keys = [
            'dh.xmlapi.username',
            'dh.xmlapi',
            'dh',
        ];
        foreach ($keys as $key) {
            $val = $this->configService->get($key);
            fpr($val);
        }
//*/
        #$val = $this->configService->get('supp.comm.tett');
        #fpr($val);
        #$this->configService->set('supp.comm.tett', 'new-value');
        #$val = $this->configService->get('supp.comm.tett');
        #fpr($val);
#
/*
        $skus = [
            'AS-4058CG',
            'BTE-ST-AS-NB12-TP200SA',
            'DH-0013C003CA',
            'EP-EE-HD-VT-D400SU3BK',
            'ING-4058CG',
            'SYN-6108179',
            'TAK-EPS.PTR.INK.ART1430',
            'TD-8427CJ',
        ];
        foreach ($skus as $sku) {
            $price = $this->pricelistService->getPrice($sku);
            $title = $this->pricelistService->getTitle($sku);
            fpr("sku=$sku, price=$price, title=`$title`");
        }
//*/
        //$this->runJob('job/Test');
    }
}
