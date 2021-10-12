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
}
