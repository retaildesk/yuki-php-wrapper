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
class AccountingInfo extends Yuki
{

    const WS_SERVICE = 'Accounting.asmx?WSDL';

    public function __construct()
    {
        parent::__construct(self::WS_SERVICE);
    }
    /**
     * Process Sales Invoice
     * @param string $salesInvoice
     * @return stdclass
     * @throws \Exception
     */
    public function processJournal($journal)
    {
        // Check for sessionId first
        if (!$this -> getSessionID()) {
            throw new Exception\InvalidSessionIDException();
        }
        // Check for sessionId first
        if (!$this -> getAdministrationID()) {
            throw new Exception\InvalidAdministrationIDException();
        }

        $xmlDoc = '<Journal>';
        $xmlDoc .= $journal;
        $xmlDoc .= '</Journal>';


        $xmlvar = new \SoapVar('<ns1:xmlDoc>' . $xmlDoc . '</ns1:xmlDoc>', \XSD_ANYXML);

        $request = array(
            "sessionID"        => $this -> getSessionID(),
            "administrationID" => $this -> getAdministrationID(),
            "xmlDoc"           => $xmlvar);

        try {
            $result = $this -> soap -> ProcessJournal($request);
        } catch (\Exception $ex) {
            // Just pass the exception through and let the index handle the exception
            throw $ex;
        }
        return simplexml_load_string($result -> ProcessJournalResult);
    }



}
