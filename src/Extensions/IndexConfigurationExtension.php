<?php

namespace SilverStripe\ForagerSubsites\Extensions;

use SilverStripe\Core\Extension;
use SilverStripe\Forager\Interfaces\DataObjectDocumentInterface;
use SilverStripe\Forager\Interfaces\DocumentInterface;

class IndexConfigurationExtension extends Extension
{

    public function updateIndexesForDocument(DocumentInterface $doc, array &$indexes): void
    {
        // Skip if document object does not implement DataObject interface
        if (!$doc instanceof DataObjectDocumentInterface) {
            return;
        }

        // Which whether the data object has the SubsiteID
        if (!$doc->getDataObject()->hasField('SubsiteID')) {
            $this->updateDocumentWithoutSubsite($doc, $indexes);
        } else {
            $this->updateDocumentWithSubsite($indexes, (int) $doc->getDataObject()->SubsiteID);
        }
    }

    /**
     * DataObject does not have a defined SubsiteID. So if the developer explicitly defined the dataObject to be
     * included in the Subsite Index configuration then allow the dataObject to be added in.
     */
    protected function updateDocumentWithoutSubsite(DocumentInterface $doc, array &$indexes): void
    {
        foreach ($indexes as $indexName => $data) {
            // DataObject explicitly defined on Subsite index definition
            $explicitClasses = $data['includeClasses'] ?? [];

            if (!isset($explicitClasses[$doc->getDataObject()->ClassName])) {
                unset($indexes[$indexName]);

                break;
            }
        }
    }

    protected function updateDocumentWithSubsite(array &$indexes, int $docSubsiteId): void
    {
        foreach ($indexes as $indexName => $data) {
            $subsiteId = $data['subsite_id'] ?? 'all';

            if ($subsiteId === 'all' || $docSubsiteId === (int) $subsiteId) {
                continue;
            }

            unset($indexes[$indexName]);
        }
    }

}
