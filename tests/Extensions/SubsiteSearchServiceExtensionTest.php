<?php

namespace SilverStripe\Subsites\Tests\Extensions;

use SilverStripe\Dev\SapphireTest;
use SilverStripe\Forager\DataObject\DataObjectDocument;
use SilverStripe\Forager\Tasks\SearchConfigure;
use SilverStripe\ForagerSubsites\Extensions\SubsiteSearchServiceExtension;
use SilverStripe\ForagerSubsites\Tests\Fake\SubsiteDataObjectFake;
use SilverStripe\ForagerSubsites\Tests\SearchServiceTestTrait;

class SubsiteSearchServiceExtensionTest extends SapphireTest
{
    use SearchServiceTestTrait;


    protected static $extra_dataobjects = [
        SubsiteDataObjectFake::class,
    ];

    protected static $fixture_file = [
        '../fixtures.yml'
    ];

    public function testGetIndexSuffixesForSubsite(): void
    {
        $this->mockConfig(true);
        $objMain = $this->objFromFixture(SubsiteDataObjectFake::class, 'two');
        $objSubsite = $this->objFromFixture(SubsiteDataObjectFake::class, 'one');

        $doc1 = DataObjectDocument::create($objMain);
        $doc2 = DataObjectDocument::create($objSubsite);

        $searchExtension = new SubsiteSearchServiceExtension();
        $reflectionMethod = new \ReflectionMethod($searchExtension, 'getIndexSuffixesForSubsite');
        $reflectionMethod->setAccessible(true);

        $suffixesMainSite = $reflectionMethod->invoke($searchExtension, $doc1);
        $suffixesSubsite = $reflectionMethod->invoke($searchExtension, $doc2);

        $this->assertEquals(['index1'], $suffixesMainSite, 'Main site should only be indexed in index1');
        $this->assertEquals(['index2'], $suffixesSubsite, 'Subsite should only be indexed in index2');
    }

    public function testUpdateAddToIndexes(): void
    {
        $this->mockConfig(true);
        $objMain = $this->objFromFixture(SubsiteDataObjectFake::class, 'two');
        $objSubsite = $this->objFromFixture(SubsiteDataObjectFake::class, 'one');

        $doc1 = DataObjectDocument::create($objMain);
        $doc2 = DataObjectDocument::create($objSubsite);

        $indexSuffixes = ['index1', 'index2'];
        $searchExtension = new SubsiteSearchServiceExtension();
        $searchExtension->updateAddToIndexes($indexSuffixes, $doc1);
        $this->assertEquals(['index1'], $indexSuffixes, 'We should only keep index2 for the Subsite index');

        $indexSuffixes = ['index1', 'index2'];
        $searchExtension->updateAddToIndexes($indexSuffixes, $doc2);
        $this->assertEquals(['index2'], $indexSuffixes, 'We should only keep index1 for Main site index');
    }

    public function testUpdateRemoveFromIndexes(): void
    {
        $this->mockConfig(true);
        $objMain = $this->objFromFixture(SubsiteDataObjectFake::class, 'two');
        $objSubsite = $this->objFromFixture(SubsiteDataObjectFake::class, 'one');

        $doc1 = DataObjectDocument::create($objMain);
        $doc2 = DataObjectDocument::create($objSubsite);

        $indexSuffixes = ['index1', 'index2'];
        $searchExtension = new SubsiteSearchServiceExtension();
        $searchExtension->updateRemoveFromIndexes($indexSuffixes, $doc1);
        $this->assertEquals(['index1'], $indexSuffixes, 'We should only keep index2 for the Subsite index');

        $indexSuffixes = ['index1', 'index2'];
        $searchExtension->updateRemoveFromIndexes($indexSuffixes, $doc2);
        $this->assertEquals(['index2'], $indexSuffixes, 'We should only keep index1 for Main site index');
    }

}
