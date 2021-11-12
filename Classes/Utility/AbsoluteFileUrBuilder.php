<?php

declare(strict_types=1);

namespace DFAU\ToujouApi\Utility;

use TYPO3\CMS\Core\Utility\GeneralUtility;

class AbsoluteFileUrBuilder
{
    public function getAbsoluteUrl(string $url): ?string
    {
        if (\preg_match('/^(?:[a-z]+:)?\/\//', $url)) {
            return $url;
        }

        return  GeneralUtility::getIndpEnv('TYPO3_SITE_URL') . $url;
    }
}
