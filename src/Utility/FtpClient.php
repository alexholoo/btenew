<?php

$ftp = array(
    'DH' => array(
        'Host' => 'ftp.dandh.com',
        'User' => '8007120000',
        'Pass' => 'hJcc7ngk',
        'File' => 'ITEMLIST',           // Server Filename
        'Save' => 'DH-ITEMLIST'         // Local Filename
    ),
 
    'SYN' => array(
        'Host' => 'ftp.synnex.ca',
        'User' => 'c1150897',
        'Pass' => '8aj2agkl',
        'File' => 'c1150897.zip',       // Server Filename
        'Save' => 'syn-c1150897.zip'    // Local Filename
    ),
);

class FtpClient
{
    protected $info;
    protected $host;
    protected $user;
    protected $pass;

    public function __construct($info)
    {
        $this->info = $info;
        $this->host = $info['Host'];
        $this->user = $info['User'];
        $this->pass = $info['Pass'];
    }

    public function download($remotefile = '', $localfile = '')
    {
    }
}

function ftp_get_pricelist($info)
{
    $ftp_host = $info['Host'];
    $ftp_user = $info['User'];
    $ftp_pass = $info['Pass'];

    // connect
    $conn = ftp_connect($ftp_host);
    if (!$conn) {
        echo "Couldn't connect to $ftp_host<br>\n";
        return;
    }
    
    // login 
    if (@ftp_login($conn, $ftp_user, $ftp_pass)) {
        ftp_pasv($conn, true);
        echo "Connected as $ftp_user@$ftp_host<br>\n";
    } else {
        echo "Couldn't connect as $ftp_user<br>\n";
    }
    
    // download
    $server_file = $info['File'];
    $local_file  = './data/csv/'.$info['Save'];

    if (ftp_get($conn, $local_file, $server_file, FTP_BINARY)) {
        echo "Successfully written to $local_file.<br><br>\n";
    } else {
        echo "There was a problem: $ftp_host.<br><br>\n";
    }
    
    if($ftp_host=='ftp.dandh.com'){
        $server_file = 'TRACKING';
        $local_file  = './out/shipping/DH-TRACKING';

        if (ftp_get($conn, $local_file, $server_file, FTP_BINARY)) {
            echo "Successfully written to ./out/shipping/DH-TRACKING.<br><br>\n";
        } else {
            echo "There was a problem: $ftp_host.<br><br>\n";
        }
    
    }
    
    ftp_close($conn);
}
