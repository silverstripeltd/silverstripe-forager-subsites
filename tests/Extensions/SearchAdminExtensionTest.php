<?php

namespace SilverStripe\ForagerSubsites\Tests\Extensions;

use SilverStripe\Assets\File;
use SilverStripe\Assets\Image;
use SilverStripe\Dev\SapphireTest;
use SilverStripe\Forager\DataObject\DataObjectDocument;
use SilverStripe\Forager\Extensions\SearchServiceExtension;
use SilverStripe\Forager\Service\IndexData;
use SilverStripe\Forager\Admin\SearchIndexAdmin;
use SilverStripe\Forager\Tests\Fake\DataObjectFake;
use SilverStripe\Forager\Tests\Fake\DataObjectFakeVersioned;
use SilverStripe\Forager\Tests\Fake\ServiceFake;
use SilverStripe\ForagerSubsites\Tests\Fake\SubsiteDataObjectFake;
use SilverStripe\ForagerSubsites\Tests\SearchServiceTestTrait;
use SilverStripe\ORM\DataQuery;
use SilverStripe\ForagerSubsites\Extensions\SearchAdminExtension;

class SearchAdminExtensionTest extends SapphireTest
{

    use SearchServiceTestTrait;

    protected static $extra_dataobjects = [
        SubsiteDataObjectFake::class,
    ];

    protected static $fixture_file = [
        '../fixtures.yml'
    ];

    public function testUpdateQuery(): void
    {
        $this->mockConfig(true);
        $this->mockService();
        $formExtension = new SearchAdminExtension();

        $configuration = SearchServiceExtension::singleton()->getConfiguration();

        foreach ($configuration->getIndexConfigurations() as $indexSuffix => $data) {
            $indexData = $configuration->getIndexDataForSuffix($indexSuffix);
            $indexData->withIndexContext(
                function (IndexData $index) use($formExtension): void {
                    foreach ($index->getClasses() as $class) {
                        $query = new DataQuery($class);

                        if (property_exists($class, 'ShowInSearch')) {
                            $query->where('ShowInSearch = 1');
                        }

                        $this->assertEquals(2, $query->count());

                        $formExtension->updateQuery($query, $index, $class);
                        $this->assertEquals(1, $query->count());
                    }
                }
            );
        }
    }
}