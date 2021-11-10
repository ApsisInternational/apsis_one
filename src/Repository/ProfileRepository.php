<?php

namespace Apsis\One\Repository;

use Apsis\One\Model\EntityInterface as EI;
use Apsis\One\Model\Profile;
use Throwable;

class ProfileRepository extends AbstractRepository
{
    /**
     * @param string $uuid
     *
     * @return Profile|null
     */
    public function findOneByIntegrationId(string $uuid): ?Profile
    {
        try {
            return $this->hydrateOne(
                $this->db->select($this->buildSqlQuery($this->buildWhereClause([EI::C_ID_INTEGRATION => $uuid])))
            );
        } catch (Throwable $e) {
            $this->logger->logErrorMsg(__METHOD__, $e);
            return null;
        }
    }

    /**
     * @param string $customerId
     *
     * @return Profile|null
     */
    public function findOneByCustomerId(string $customerId): ?Profile
    {
        try {
            $sql = $this->buildSqlQuery(
                $this->buildWhereClause([EI::C_ID_CUSTOMER => $customerId, EI::C_IS_CUSTOMER => EI::YES])
            );
            return $this->hydrateOne($this->db->select($sql));
        } catch (Throwable $e) {
            $this->logger->logErrorMsg(__METHOD__, $e);
            return null;
        }
    }

    /**
     * @param string $newsletterId
     *
     * @return Profile|null
     */
    public function findOneByNewsletterId(string $newsletterId): ?Profile
    {
        try {
            $sql = $this->buildSqlQuery(
                $this->buildWhereClause([EI::C_ID_CUSTOMER => $newsletterId, EI::C_IS_NEWSLETTER => EI::YES])
            );
            return $this->hydrateOne($this->db->select($sql));
        } catch (Throwable $e) {
            $this->logger->logErrorMsg(__METHOD__, $e);
            return null;
        }
    }

    /**
     * @param string $email
     * @param int $idShop
     * @param bool $isCustomer
     * @param bool $isNewsletter
     * @param bool $isPartnerOffers
     * @param bool $isGuest
     *
     * @return Profile|null
     */
    public function findOneByEmailForGivenShop(
        string $email,
        int $idShop,
        bool $isCustomer = false,
        bool $isNewsletter = false,
        bool $isPartnerOffers = false,
        bool $isGuest = false
    ): ?Profile {
        try {
            $conditions = [EI::C_EMAIL => $email, EI::C_ID_SHOP => $idShop];
            if ($isCustomer) {
                $conditions[EI::C_IS_CUSTOMER] = $isCustomer;
            }
            if ($isNewsletter) {
                $conditions[EI::C_IS_NEWSLETTER] = $isNewsletter;
            }
            if ($isPartnerOffers) {
                $conditions[EI::C_IS_OFFERS] = $isPartnerOffers;
            }
            if ($isGuest) {
                $conditions[EI::C_IS_GUEST] = $isGuest;
            }

            $sql =  sprintf(
                'SELECT * FROM %s WHERE %s LIMIT 0 , 1',
                $this->getTableNameWithPrefix(), $this->buildWhereClause($conditions)
            );
            return $this->hydrateOne($this->db->select($sql));
        } catch (Throwable $e) {
            $this->logger->logErrorMsg(__METHOD__, $e);
            return null;
        }
    }
}
