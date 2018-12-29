<?php
namespace dhope0000\LXDClient\Model\Client;

use GuzzleHttp\Client as GuzzleClient;
use Http\Adapter\Guzzle6\Client as GuzzleAdapter;
use \Opensaucesystems\Lxd\Client;
use dhope0000\LXDClient\Constants\Constants;
use dhope0000\LXDClient\Model\Hosts\GetDetails;

class LxdClient
{
    private $clientBag = [];

    public function __construct(GetDetails $getDetails)
    {
        $this->getDetails = $getDetails;
    }

    public function getClientByUrl($url, $checkCache = true)
    {
        $hostId = $this->getDetails->getIdByUrlMatch($url);
        return $this->getANewClient($hostId, $checkCache);
    }

    public function getANewClient($hostId, $checkCache = true)
    {
        $hostDetails = $this->getDetails->getAll($hostId);

        if (empty($hostDetails)) {
            throw new \Exception("Couldn't find info for this host", 1);
        }

        if ($checkCache && isset($this->clientBag[$hostDetails["Host_Url_And_Port"]])) {
            return $this->clientBag[$hostDetails["Host_Url_And_Port"]];
        }

        $certPath = $this->createFullcertPath($hostDetails["Host_Cert_Path"]);
        $config = $this->createConfigArray($certPath);
        return $this->createNewClient($hostDetails["Host_Url_And_Port"], $config);
    }

    private function createFullcertPath(string $certName)
    {
        return Constants::CERTS_DIR . $certName;
    }

    public function createConfigArray($certLocation)
    {
        $certPath = realpath($certLocation);

        if ($certPath === false) {
            throw new \Exception("Certificate has gone walk abouts", 1);
        }

        return [
            'verify' => false,
            'cert' => [
                $certPath,
                ''
            ]
        ];
    }

    public function createNewClient($urlAndPort, $config)
    {
        $guzzle = new GuzzleClient($config);
        $adapter = new GuzzleAdapter($guzzle);
        $client = new Client($adapter);
        $client->setUrl($urlAndPort);
        $this->clientBag[$urlAndPort] = $client;
        return $client;
    }
}
