<?php

namespace Gorg\Bundle\JiraBundle\Services;


class JiraClient
{
    private $user;
    private $server;
    private $port;
    private $wsdl;
    private $password;

    public function __construct($server, $port, $wsdl, $user, $password)
    {
        $this->server   = $server;
        $this->port     = $port;
        $this->wsdl     = $wsdl;
        $this->user     = $user;
        $this->password = $password;
    }

    public function createIssue($project, $subject, $body)
    {
        $soap = new \SoapClient ("http://" . $this->server . ":" . $this->port . $this->wsdl,
            array ("style" => SOAP_RPC,"use" => SOAP_ENCODED,"soap_version" => SOAP_1_1,"uri" => "urn:myWS"));

        $auth = $soap->login ($this->user, $this->password);
        if ($auth)
        {
            $rIssue['project'] = $project;
            $rIssue['type'] = '2';
            $rIssue['summary'] = $subject;
            $rIssue['description'] = $body;
            $rIssue['priority'] = '4';
            $rIssue['reporter'] = $this->user;
            $result = $soap->createIssue ($auth, $rIssue);
            $soap->logout ($this->user);
        }
        return $result->key;
   }
}
