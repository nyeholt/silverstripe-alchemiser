<?php
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
		
		$service = singleton('AlchemyService');
		
		$cat = $service->getCategoryFor($content);
		$keywords = $service->getKeywordsFor($content);
		$result = $service->getEntitiesFor($content);
	}
}
