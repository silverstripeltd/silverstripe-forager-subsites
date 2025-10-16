<?php

namespace SilverStripe\ForagerSubsites\Tests\Extensions;

use SilverStripe\Dev\SapphireTest;
use SilverStripe\ForagerSubsites\Extensions\IndexConfigurationExtension;
use SilverStripe\ForagerSubsites\Tests\Fake\SubsiteDataObjectFake;
use SilverStripe\ForagerSubsites\Tests\SearchServiceTestTrait;
use SilverStripe\Forager\DataObject\DataObjectDocument;
use SilverStripe\Forager\Extensions\SearchServiceExtension;

class IndexConfigurationExtensionTest extends SapphireTest
{
    use SearchServiceTestTrait;

    protected static $extra_dataobjects = [
        SubsiteDataObjectFake::class,
    ];

    protected static $fixture_file = [
        '../fixtures.yml'
    ];

    public function testUpdateIndexesForDocument(): void
    {
        $this->mockConfig(true);

        $dataObject = $this->objFromFixture(SubsiteDataObjectFake::class, 'one');

        $doc = DataObjectDocument::create($dataObject);

        $configuration = SearchServiceExtension::singleton()->getConfiguration();
        $indexes = $configuration->getIndexConfigurations();
        $this->assertEquals(2, count($indexes));

        $indexExtension = new IndexConfigurationExtension();
        $indexExtension->updateIndexesForDocument($doc, $indexes);

        $this->assertEquals(1, count($indexes));
        $this->assertArrayHasKey('index2', $indexes);
    }
}