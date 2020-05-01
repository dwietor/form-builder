<?php

/*
 * This file is part of the DWietor CMS Form Builder.
 *
 * (c) David Wietor <davidwietor@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DWietor\Bundle\CmsFormBundle\Cache;

use DWietor\Bundle\CmsFormBundle\Validator\Loader\ValidationRuleLoader;
use Symfony\Component\HttpKernel\CacheClearer\CacheClearerInterface;
use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmerInterface;

class CacheClearer implements CacheWarmerInterface, CacheClearerInterface
{
    /** @var ValidationRuleLoader */
    protected $ruleLoader;

    /**
     * @param ValidationRuleLoader $ruleLoader
     */
    public function __construct(ValidationRuleLoader $ruleLoader)
    {
        $this->ruleLoader = $ruleLoader;
    }

    /**
     * {@inheritdoc}
     */
    public function warmUp($cacheDir)
    {
        $this->ruleLoader->getForForm('dummy-call');
    }

    /**
     * {@inheritdoc}
     */
    public function isOptional()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function clear($cacheDir)
    {
        $this->ruleLoader->clearCache();
    }
}
