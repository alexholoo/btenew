<?php
namespace App\Controllers;

use Supplier\Factory;
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
        //$config = $this->config->toArray();
        //fpr($config['xmlapi']['synnex']);
        //
        //$config = $this->config->xmlapi->toArray();
        //fpr($config['synnex']);
#
        //$amazonService = $this->AmazonService;
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
        #$val = $this->ConfigService->get('dh.xmlapi.username');
        #fpr($val);

        #$val = $this->ConfigService->get('dh.xmlapi');
        #fpr($val);

        #$val = $this->ConfigService->get('dh');
        #fpr($val);

        #$val = $this->ConfigService->get('supp.comm.tett');
        #fpr($val);

        #$this->ConfigService->set('supp.comm.tett', 'new-value');
        #$val = $this->ConfigService->get('supp.comm.tett');
        #fpr($val);
#
        #$price = $this->PricelistService->getPrice('SYN-6108179');
        #fpr($price);
        #$title = $this->PricelistService->getTitle('SYN-6108179');
        #fpr($title);

        #$price = $this->PricelistService->getPrice('DH-0013C003CA');
        #fpr($price);
        #$title = $this->PricelistService->getTitle('DH-0013C003CA');
        #fpr($title);
    }
}
