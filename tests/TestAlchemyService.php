<?php
/*

Copyright (c) 2009, SilverStripe Australia PTY LTD - www.silverstripe.com.au
All rights reserved.

Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:

    * Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.
    * Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the
      documentation and/or other materials provided with the distribution.
    * Neither the name of SilverStripe nor the names of its contributors may be used to endorse or promote products derived from this software
      without specific prior written permission.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE
LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE
GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT,
STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY
OF SUCH DAMAGE.
*/

/**
 * 
 *
 * @author Marcus Nyeholt <marcus@silverstripe.com.au>
 */
class TestAlchemyService extends SapphireTest {

    public function testEntities() {
		$content = <<<TEXT
CNN: NEW YORK (CNNMoney.com) -- President Obama will ask the Wall Street community that he wants to reform to join in his effort.

In remarks prepared for delivery at Cooper Union in New York later Thursday, the president said legislation in both houses of Congress aimed at reforming the banking industry represents "significant improvement on the flawed rules we have in place today."

Obama said he's sure many of the lobbyists working to defeat the measure are acting on behalf of the Wall Street firms represented by members of the audience.
TEXT;
		AlchemyService::set_api_key('');
		AlchemyService::$methods['getEntities']['cache'] = 0;
		$service = singleton('AlchemyService');
		
		$result = $service->getEntities($content);

		print_r($result);
	}
}

?>