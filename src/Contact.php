<?php

/*
 * Copyright 2017 Bert Maurau.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace Yuki;

/**
 * Description of the Yuki Sales Sub service
 *
 * @author Bert Maurau
 */
class Contact extends Yuki
{

    const WS_SERVICE = 'Contact.asmx?WSDL';

    public function __construct()
    {
        parent::__construct(self::WS_SERVICE);
    }

    /**
     * List all active ledgers that are available for the given Session ID
     * @return array List of Administrations
     * @throws InvalidSessionIDException
     * @throws \Exception
     */
    public function all()
    {
        // Check for sessionId first
        if (!$this -> getSessionID()) {
            throw new Exception\InvalidSessionIDException();
        }


        $customers = [];
        try {
            $count = 100;
            $page_number = 1;
            while($count == 100){
                $request = array(
                    "sessionID" => $this -> getSessionID(),
                    "searchOption"=>"All",
                    "sortOrder"=>"Name",
                    "active"=>"Both",
                    "pageNumber"=>$page_number
                );

                $result = json_decode(json_encode((array)simplexml_load_string($this -> soap -> SearchContacts($request) -> SearchContactsResult->any)), TRUE)['Contact'];
                $count = count($result);
                $customers = array_merge($customers,$result);
                $page_number++;
            }
            return $customers;
        } catch (\Exception $ex) {
            // Just pass the exception through and let the index handle the exception
            throw $ex;
        }
        return $customers;
    }
    /**
     * Update Contact https://support.yuki.nl/nl/support/solutions/articles/11000063071-updatecontact-sessionid-domainid-xmldoc-
     * @param array $contact
     * @return stdclass
     * @throws \Exception
     */
    public function updateContact($contact){
        // Check for sessionId first
        if (!$this -> getSessionID()) {
            throw new Exception\InvalidSessionIDException();
        }
        // Check for sessionId first
        if (!$this -> getAdministrationID()) {
            throw new Exception\InvalidAdministrationIDException();
        }
        // Check for given domain
        if (!$contact) {
            return false;
        } else {
            $xmlDoc = $contact->renderXml();
        }

        $xmlvar = new \SoapVar('<ns1:xmlDoc>' . $xmlDoc . '</ns1:xmlDoc>', \XSD_ANYXML);
        $request = array(
            "sessionId"        => $this -> getSessionID(),
            "administrationId" => $this -> getAdministrationID(),
            "xmlDoc"           => $xmlvar);

        try {
            $result = $this->soap->UpdateContact($request);
        } catch (\Exception $ex) {
            // Just pass the exception through and let the index handle the exception
            throw $ex;
        }
        return $this -> parseXMLResponse($result->UpdateContactResult->any);
    }
}
