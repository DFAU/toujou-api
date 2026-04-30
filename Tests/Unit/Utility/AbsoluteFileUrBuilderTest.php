<?php

declare(strict_types=1);

namespace DFAU\ToujouApi\Tests\Unit\Utility;

use DFAU\ToujouApi\Utility\AbsoluteFileUrBuilder;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

final class AbsoluteFileUrBuilderTest extends UnitTestCase
{
    /** @var AbsoluteFileUrBuilder */
    private $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->subject = new AbsoluteFileUrBuilder();

        GeneralUtility::setIndpEnv('TYPO3_SITE_URL', 'http:://www.typo3.test/');
    }

    #[Test]
    public function it_can_return_absolute_url_for_files(): void
    {
        $result = $this->subject->getAbsoluteUrl('fileadmin/user_upload.jpg');

        $this->assertEquals('http:://www.typo3.test/fileadmin/user_upload.jpg', $result);
    }

    #[Test]
    public function it_wont_prepend_site_url_on_absolute_urls(): void
    {
        $result = $this->subject->getAbsoluteUrl('https://example.com/image.jpeg?fl');

        $this->assertEquals('https://example.com/image.jpeg?fl', $result);
    }
}
