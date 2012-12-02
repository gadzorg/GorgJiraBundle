<?php

namespace Gorg\Bundle\JiraBundle\Services;


class JiraClient
{
    private $user;
    private $server;
    private $port;
    private $wsdl;
    private $password;
    private $logger;

    public function __construct($logger, $server, $port, $wsdl, $user, $password)
    {
        $this->server   = $server;
        $this->port     = $port;
        $this->wsdl     = $wsdl;
        $this->user     = $user;
        $this->password = $password;
        $this->logger   = $logger;
    }

    public function createIssue($project, $subject, $body, $extraField = array(), $components = array())
    {
        $soap = new \SoapClient ($this->server . ":" . $this->port . $this->wsdl,
            array ("style" => SOAP_RPC,"use" => SOAP_ENCODED,"soap_version" => SOAP_1_1,"uri" => "urn:myWS"));

        $auth = $soap->login ($this->user, $this->password);
        if ($auth)
        {
            $rIssue['project'] = $project;
            $rIssue['type'] = '2';
            $rIssue['summary'] = $subject;
            foreach($components as $componentId => $componentName)
            {
               $rIssue['components'][] = array('id' => $componentId, 'name' => $componentName);
            }
            $rIssue['description'] = $body;
            $rIssue['priority'] = '4';
            $rIssue['reporter'] = $this->user;
            foreach($extraField as $fieldId => $fieldValue)
            {
                $rIssue['customFieldValues'][] = array('customfieldId' => $fieldId, 'values' => array($fieldValue));
            }
            $result = $soap->createIssue ($auth, $rIssue);
            $soap->logout ($this->user);
            $this->logger->info("Create issue " . $result->key . ": " . $subject);
        }
        return $result->key;
   }
}

