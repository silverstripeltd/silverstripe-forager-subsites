<?php

namespace SilverStripe\ForagerSubsites\Extensions;

use InvalidArgumentException;
use SilverStripe\Core\Extension;
use SilverStripe\Forager\Service\IndexData;

/**
 * @property IndexData $owner
 */
class IndexDataExtension extends Extension
{

    public const string INDEX_SUBSITE_PROP = 'subsite_id';

    public function getSubsite(): string
    {
        $data = $this->owner->getData();

        if (!array_key_exists(self::INDEX_SUBSITE_PROP, $data)) {
            throw new InvalidArgumentException(
                sprintf('No subsite found on index suffix: "%s"', $this->owner->getSuffix())
            );
        }

        return $data[self::INDEX_SUBSITE_PROP] ?? '';
    }

}
