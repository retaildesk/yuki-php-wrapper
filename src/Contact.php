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

        $request = array(
            "sessionID" => $this -> getSessionID(),
            "searchOption"=>"All",
            "sortOrder"=>"Name",
            "active"=>"Active",
            "pageNumber"=>1
        );
        try {
            $result = $this -> soap -> SearchContacts($request);
        } catch (\Exception $ex) {
            // Just pass the exception through and let the index handle the exception
            throw $ex;
        }
        return json_decode(json_encode((array)simplexml_load_string($result -> SearchContactsResult->any)), TRUE)['contact'];
        // Return the list of Contacts
    }
}
