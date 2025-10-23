<?php

namespace SilverStripe\ForagerSubsites\Extensions;

use SilverStripe\Core\Extension;
use SilverStripe\Forager\Service\IndexData;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\DataQuery;

class SearchAdminExtension extends Extension
{

    public function updateQuery(DataQuery $query, IndexData $index, string $class = ''): void
    {
        $data = $index->getData();

        if (!isset($data['subsite_id']) || !is_numeric($data['subsite_id'])) {
            return;
        }

        // If the DataObject has a Subsite relation, then apply a SubsiteID filter
        if (!$class || !DataObject::getSchema()->hasOneComponent($class, 'Subsite')) {
            return;
        }

        $query->where(sprintf('SubsiteID IS NULL OR SubsiteID = %d', $data['subsite_id']));
    }

}
