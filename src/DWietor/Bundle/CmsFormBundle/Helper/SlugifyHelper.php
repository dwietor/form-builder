<?php

/*
 * This file is part of the DWietor CMS Form Builder.
 *
 * (c) David Wietor <davidwietor@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DWietor\Bundle\CmsFormBundle\Helper;

class SlugifyHelper
{
    /**
     * Copy of @see \Oro\Bundle\EntityConfigBundle\Generator\SlugGenerator
     *
     * @param string $string
     * @return string
     */
    public static function slugify(string $string): string
    {
        $string = transliterator_transliterate(
            "Any-Latin;
            Latin-ASCII;
            NFD;
            [:Nonspacing Mark:] Remove;
            [^\u0020\u002D\u0030-\u0039\u0041-\u005A\u0041-\u005A\u005F\u0061-\u007A\u007E] Remove;
            NFC;
            Lower();",
            $string
        );
        $string = preg_replace('/[-\s]+/', '-', $string);

        return trim($string, '-');
    }
}
