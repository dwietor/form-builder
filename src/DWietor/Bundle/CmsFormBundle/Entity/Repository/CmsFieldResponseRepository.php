<?php

/*
 * This file is part of the DWietor CMS Form Builder.
 *
 * (c) David Wietor <davidwietor@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DWietor\Bundle\CmsFormBundle\Entity\Repository;

use DWietor\Bundle\CmsFormBundle\Entity\CmsFieldResponse;
use Doctrine\ORM\EntityRepository;

class CmsFieldResponseRepository extends EntityRepository
{
    /**
     * @param array $formResponsesIds
     * @return array
     */
    public function findGroupedByFormResponses(array $formResponsesIds = [])
    {
        /** @var CmsFieldResponse[] $records */
        $records = $this->findBy(['formResponse' => $formResponsesIds]);

        $groupedByResponses = [];
        foreach ($records as $record) {
            // @todo create option 'hide_from_response'
            if ($record->getField()->getType() === 'oro-recaptcha-v3') {
                continue;
            }
            $responseId = $record->getFormResponse()->getId();
            if (array_key_exists($responseId, $groupedByResponses)) {
                $groupedByResponses[$responseId][] = $record;
            } else {
                $groupedByResponses[$responseId] = [$record];
            }
        }

        return $groupedByResponses;
    }
}
