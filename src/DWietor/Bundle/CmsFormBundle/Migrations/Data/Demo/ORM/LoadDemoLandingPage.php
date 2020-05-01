<?php

/*
 * This file is part of the DWietor CMS Form Builder.
 *
 * (c) David Wietor <davidwietor@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DWietor\Bundle\CmsFormBundle\Migrations\Data\Demo\ORM;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Oro\Bundle\CMSBundle\Migrations\Data\AbstractLoadPageData;

class LoadDemoLandingPage extends AbstractLoadPageData implements DependentFixtureInterface
{
    /**
     * {@inheritdoc}
     */
    public function getDependencies()
    {
        return [LoadDemoCmsForm::class];
    }

    /**
     * @return string
     */
    protected function getFilePaths()
    {
        return $this->getFilePathsFromLocator('@DWietorCmsFormBundle/Migrations/Data/Demo/ORM/data/pages.yml');
    }
}
