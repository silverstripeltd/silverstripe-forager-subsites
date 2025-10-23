<?php

namespace SilverStripe\ForagerSubsites\Service;

use SilverStripe\Core\Injector\Injectable;
use SilverStripe\Forager\Interfaces\IndexDataContextProvider;
use SilverStripe\Forager\Service\IndexData;
use SilverStripe\Subsites\Model\Subsite;
use SilverStripe\Subsites\State\SubsiteState;

class SubsiteIndexDataContext implements IndexDataContextProvider
{

    use Injectable;

    public function getContext(): callable
    {
        return static function (callable $next, IndexData $indexData): mixed {
            $subsiteId = $indexData->getSubsite() ?? 'all';

            if ($subsiteId !== 'all' && is_numeric($subsiteId)) {
                return SubsiteState::singleton()->withState(function (SubsiteState $newState) use ($next, $subsiteId) {
                    $newState->setSubsiteId($subsiteId);

                    return $next();
                });
            }

            Subsite::disable_subsite_filter(true);

            return $next();
        };
    }

}
