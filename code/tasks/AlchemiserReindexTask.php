<?php
/**
 * Reindex the entire content of the current system in the solr backend
 *
 * @author Marcus Nyeholt <marcus@silverstripe.com.au>
 */
class AlchemiserReindexTask extends BuildTask
{
    protected $title = "Reindex all content via AlchemyAPI";

    protected $description = "Iterates through all content within the system, re-indexing it via AlchemyAPI";

    public function run($request)
    {
        // get the holders first, see if we have any that AREN'T in the root (ie we've already partitioned everything...)
        $pages = null;
        if (isset($_GET['ids'])) {
            $realIds = array();
            if (strpos($_GET['ids'], '-')) {
                list($low, $hi) = explode('-', $_GET['ids']);
                $low = (int) $low;
                $hi = (int) $hi;
                $realIds = range($low, $hi);
            } else {
                $ids = explode(',', $_GET['ids']);

                foreach ($ids as $id) {
                    $realIds[] = (int) $id;
                }
            }

            $in = implode(',', $realIds);
            $pages = DataObject::get('Page', 'SiteTree.ID IN ('.$in.')');
        } else {
            $pages = DataObject::get('Page');
            throw new Exception("Specify ids as either a list of ids comma separated, or a single range of lo-hi");
        }

        $alchemy = singleton('AlchemyService');


        /* @var $search SolrSearchService */
        $count = 0;
        foreach ($pages as $page) {
            $alchemy->alchemise($page, true);
            $page->write();
            $page->doPublish();
            echo "<p>Reindexed (#$page->ID) $page->Title</p>\n";
            $count ++;
        }
        echo "Reindex complete, $count objects re-indexed<br/>";
    }
}
