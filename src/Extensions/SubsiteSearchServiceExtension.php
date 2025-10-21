<?php

namespace SilverStripe\ForagerSubsites\Extensions;

use SilverStripe\Core\Extension;
use SilverStripe\Forager\Interfaces\DataObjectDocumentInterface;
use SilverStripe\Forager\Service\IndexConfiguration;

class SubsiteSearchServiceExtension extends Extension
{

    public function updateAddToIndexes(array &$indexSuffixes, DataObjectDocumentInterface $doc): void
    {
        $this->updateSuffixesForSubsiteDoc($indexSuffixes, $doc);
    }

    public function updateRemoveFromIndexes(array &$indexSuffixes, DataObjectDocumentInterface $doc): void
    {
        $this->updateSuffixesForSubsiteDoc($indexSuffixes, $doc);
    }

    private function updateSuffixesForSubsiteDoc(array &$indexSuffixes, DataObjectDocumentInterface $doc): void
    {
        // Find out which indexes this content needs to be updated in - may be smaller than configured
        $suffixesForSubsite = $this->getIndexSuffixesForSubsite($doc);

        if ($suffixesForSubsite === null) {
            // null means fall back to forager standard behaviour
            return;
        }

        // note this may be an empty array if no updates are required
        $indexSuffixes = $suffixesForSubsite;
    }

    /**
     * Identifies the indexes a dataobject should be updated in based on their subsite.
     * This is usually a reduced list depending on how the search configuration is setup for subsites
     *
     * @param DataObjectDocumentInterface $doc
     * @return array|null
     */
    private function getIndexSuffixesForSubsite(DataObjectDocumentInterface $doc): ?array
    {
        $dataObject = $doc->getDataObject();
        $docSubsiteId = $dataObject->SubsiteID ?? 0;

        // if we don't have a dataobject at this stage we can't look anything up so just bail
        if (!$dataObject) {
            return null;
        }

        $updatedIndexSuffixes = [];
        $indexConfigurations = IndexConfiguration::singleton()
            ->getIndexConfigurationsForClassName($dataObject->ClassName);

        // Find which indexes are setup for this subsite
        foreach ($indexConfigurations as $indexSuffix => $indexConfiguration) {
            $subsiteId = $indexConfiguration['subsite_id'] ?? 'all';

            if ($subsiteId !== 'all' && $docSubsiteId !== (int) $subsiteId) {
                continue;
            }

            $updatedIndexSuffixes[] = $indexSuffix;
        }

        return $updatedIndexSuffixes;
    }

}
