<?php

namespace SilverStripe\ForagerSubsites\Tests;

use Page;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\Forager\DataObject\DataObjectDocument;
use SilverStripe\Forager\Extensions\SearchServiceExtension;
use SilverStripe\Forager\Interfaces\IndexingInterface;
use SilverStripe\Forager\Service\IndexConfiguration;
use SilverStripe\Forager\Tests\Fake\IndexConfigurationFake;
use SilverStripe\ForagerSubsites\Tests\Fake\SubsiteDataObjectFake;
use SilverStripe\Forager\Tests\Fake\ServiceFake;

trait SearchServiceTestTrait
{

    protected function mockConfig(bool $setConfig = false): IndexConfiguration
    {
        $fake = new IndexConfigurationFake();

        Injector::inst()->registerService($fake, IndexConfiguration::class);

        $config = IndexConfiguration::singleton();

        // Make sure we have our usual default batch_size set (mostly only relevant for devs working on this module who
        // might have their own local config set up with a different default batch_size)
        IndexConfiguration::config()->set('batch_size', 100);

        if ($setConfig) {
            IndexConfiguration::config()->set(
                'indexes',
                [
                    'index1' => [
                        'subsite_id' => 0,
                        'context' => 'subsite',
                        'includeClasses' => [
                            SubsiteDataObjectFake::class => [
                                'batch_size' => 75,
                                'fields' => [
                                    'field1' => true,
                                    'field2' => true,
                                ],
                            ],
                        ],
                    ],
                    'index2' => [
                        'subsite_id' => 1,
                        'context' => 'subsite',
                        'includeClasses' => [
                            SubsiteDataObjectFake::class => [
                                'batch_size' => 25,
                                'fields' => [
                                    'field5' => true,
                                ],
                            ],
                        ],
                    ],
                ]
            );
        }

        SearchServiceExtension::singleton()->setConfiguration($config);

        return $config;
    }

    protected function mockService(): ServiceFake
    {
        Injector::inst()->registerService($service = new ServiceFake(), IndexingInterface::class);
        SearchServiceExtension::singleton()->setIndexService($service);

        return $service;
    }

}
