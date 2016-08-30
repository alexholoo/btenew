<?php
namespace App\Controllers;

use Supplier\XmlApi\PurchaseOrder\Synnex\Client as SynnexPOClient;
use Supplier\XmlApi\PriceAvailability\Synnex\Client as SynnexPAClient;

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
        $config = $this->config->toArray();
        //fpr($config['xmlapi']['synnex']);
        //
        //$config = $this->config->xmlapi->toArray();
        //fpr($config['synnex']);

        //$cl = new Client($config['xmlapi']['synnex']);
        //$req = $cl->createRequest();
        //$req->addOrder([]);
        //fpr($req->toXml());
        
        $amazonService = $this->AmazonService;
        $amazonService->doSomething();

        $cl = new SynnexPAClient($config['xmlapi']['synnex']);
        $req = $cl->createRequest();
        $req->addPartnum('5530299');
        $req->addPartnum('4502585');
        //fpr($req->toXml());
    }
}
