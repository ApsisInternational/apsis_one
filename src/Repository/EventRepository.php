<?php

namespace Apsis\One\Repository;

use Apsis\One\Model\EntityInterface as EI;
use Apsis\One\Model\Event;
use Throwable;

class EventRepository extends AbstractRepository
{
    /**
     * @param int $idProfile
     * @param array $syncStatus
     *
     * @return Event[]|null
     */
    public function findByProfileIdAndSyncStatus(int $idProfile, array $syncStatus): ?array
    {
        try {
            $this->logger->logInfoMsg(__METHOD__);

            return $this->hydrateMany(
                $this->db->select($this->buildSqlQuery(
                    $this->buildWhereClause([EI::C_ID_PROFILE => $idProfile, EI::C_SYNC_STATUS => $syncStatus]),
                    self::QUERY_LIMIT
                ))
            );
        } catch (Throwable $e) {
            $this->logger->logErrorMsg(__METHOD__, $e);
            return null;
        }
    }
}
