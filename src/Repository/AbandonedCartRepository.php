<?php

namespace Apsis\One\Repository;

use Apsis\One\Model\EntityInterface as EI;
use Apsis\One\Model\AbandonedCart;
use Throwable;

class AbandonedCartRepository extends AbstractRepository
{
    /**
     * @param string $token
     *
     * @return AbandonedCart|null
     */
    public function findOneByToken(string $token): ?AbandonedCart
    {
        try {
            $this->logger->logInfoMsg(__METHOD__);

            return $this->hydrateOne(
                $this->db->select($this->buildSqlQuery($this->buildWhereClause([EI::C_TOKEN => $token])))
            );
        } catch (Throwable $e) {
            $this->logger->logErrorMsg(__METHOD__, $e);
            return null;
        }
    }

    /**
     * @param array $idShopArr
     * @param string $beforeDateTime
     * @param string $afterDateTime
     *
     * @return AbandonedCart[]|null
     */
    public function findForGivenShopsFilterByDateTime(array $idShopArr, string $beforeDateTime, string $afterDateTime): ?array
    {
        try {
            $this->logger->logInfoMsg(__METHOD__);

            return $this->hydrateMany(
                $this->db->select(
                    $this->buildSqlQuery(
                        sprintf(
                            "%s BETWEEN CAST('%s' AS DATETIME) AND CAST('%s' AS DATETIME) AND %s",
                            EI::C_DATE_ADD,
                            $afterDateTime,
                            $beforeDateTime,
                            $this->buildWhereClause([EI::C_ID_SHOP => $idShopArr])
                        ),
                        self::QUERY_LIMIT
                    )
                )
            );
        } catch (Throwable $e) {
            $this->logger->logErrorMsg(__METHOD__, $e);
            return null;
        }
    }
}
