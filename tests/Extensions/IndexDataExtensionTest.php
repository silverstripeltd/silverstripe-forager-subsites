<?php

namespace SilverStripe\ForagerSubsites\Tests\Extensions;

use SilverStripe\Dev\SapphireTest;
use SilverStripe\ForagerSubsites\Extensions\IndexDataExtension;


class IndexDataExtensionTest extends SapphireTest
{

    public function testGetSubsite(): void
    {
        $extension = new IndexDataExtension();

        $owner = new class {
            public function getData(): array {
                return [
                    'subsite_id' => 'all',
                ];
            }
        };

        $extension->setOwner($owner);

        $this->assertEquals('all', $extension->getSubsite());

        $owner = new class {
            public function getData(): array {
                return [];
            }

            public function getSuffix(): string {
                return 'main';
            }
        };

        $extension->setOwner($owner);
        $this->expectExceptionMessage('No subsite found on index suffix: "main"');
        $extension->getSubsite();
    }

}
