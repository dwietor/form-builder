<?php

/*
 * This file is part of the DWietor CMS Form Builder.
 *
 * (c) David Wietor <davidwietor@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DWietor\Bundle\CmsFormBundle\ImportExport\Serializer;

use DWietor\Bundle\CmsFormBundle\Entity\CmsFieldResponse;
use Oro\Bundle\ImportExportBundle\Serializer\Normalizer\ConfigurableEntityNormalizer;

class FieldResponseNormalizer extends ConfigurableEntityNormalizer
{
    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null, array $context = array())
    {
        return $data instanceof CmsFieldResponse;
    }

    /**
     * {@inheritdoc}
     */
    public function normalize($object, $format = null, array $context = [])
    {
        $result = parent::normalize($object, $format, $context);

        if (is_array($result) && array_key_exists('value', $result)) {
            $result['value'] = is_array($result['value'])
                ? implode(', ', $object->getValue(true))
                : $object->getValue(true);
        }

        return $result;
    }
}
