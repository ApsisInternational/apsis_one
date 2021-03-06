<?php

namespace Apsis\One\Repository;

use Apsis\One\Helper\LoggerHelper;
use Apsis\One\Model\EntityInterface as EI;
use Apsis\One\Helper\EntityHelper;
use PrestaShop\PrestaShop\Core\Foundation\Database\EntityManager;
use PrestaShop\PrestaShop\Core\Foundation\Database\EntityMetaData;
use PrestaShop\PrestaShop\Core\Foundation\Database\EntityRepository;
use PrestaShop\PrestaShop\Core\Foundation\Database\Exception;
use Throwable;

abstract class AbstractRepository extends EntityRepository implements RepositoryInterface
{
    /**
     * @var LoggerHelper
     */
    protected $logger;

    /**
     * @param EntityManager $entityManager
     * @param string $tablesPrefix
     * @param EntityMetaData $entityMetaData
     */
    public function __construct(EntityManager $entityManager, string $tablesPrefix, EntityMetaData $entityMetaData)
    {
        $this->logger = new LoggerHelper();
        parent::__construct($entityManager, $tablesPrefix, $entityMetaData);
    }

    /**
     * @param string $where
     * @param string $limit
     * @param string $from
     *
     * @return string
     */
    protected function buildSqlQuery(string $where, string $limit = '0 , 1', string $from = ''): string
    {
        $primary =  EI::T_PRIMARY_MAPPINGS[$this->entityMetaData->getTableName()];
        $from = empty($from) ? $this->getTableNameWithPrefix() : $from;
        return sprintf('SELECT * FROM %s WHERE %s ORDER BY %s ASC LIMIT %s', $from, $where, $primary, $limit);
    }

    /**
     * @param array $conditions
     * @param string $andOrOr
     *
     * @return string
     *
     * @throws Exception
     */
    public function buildWhereClause(array $conditions, string $andOrOr = 'AND'): string
    {
        return $this->queryBuilder->buildWhereConditions($andOrOr, $conditions);
    }

    /**
     * @param int $id
     *
     * @return EI|null
     */
    public function findOneById(int $id): ?EI
    {
        try {
            return parent::findOne($id);
        } catch (Throwable $e) {
            $this->logger->logErrorMsg(__METHOD__, $e);
            return null;
        }
    }

    /**
     * @return EI[]|null
     */
    public function findAll(): ?array
    {
        try {
            return parent::findAll();
        } catch (Throwable $e) {
            $this->logger->logErrorMsg(__METHOD__, $e);
            return null;
        }
    }

    /**
     * @param int $idProfile
     *
     * @return EI[]|EI|null
     */
    public function findByProfileId(int $idProfile): ?array
    {
        try {
            if ($this instanceof ProfileRepository) {
                return $this->findOneById($idProfile);
            }

            $sql = $this->buildSqlQuery($this->buildWhereClause([EI::C_ID_PROFILE => $idProfile]), self::QUERY_LIMIT);
            return $this->hydrateMany($this->db->select($sql));
        } catch (Throwable $e) {
            $this->logger->logErrorMsg(__METHOD__, $e);
            return null;
        }
    }

    /**
     * @param int $idProfile
     * @param array $idShopArr
     *
     * @return EI[]|null
     */
    public function findByProfileIdForGivenShop(int $idProfile, array $idShopArr): ?array
    {
        try {
            $sql = $this->buildSqlQuery(
                $this->buildWhereClause([EI::C_ID_PROFILE => $idProfile, EI::C_ID_SHOP => $idShopArr]),
                self::QUERY_LIMIT
            );
            return $this->hydrateMany($this->db->select($sql));
        } catch (Throwable $e) {
            $this->logger->logErrorMsg(__METHOD__, $e);
            return null;
        }
    }

    /**
     * @param array $statusArr
     * @param array $idShopArr
     * @param int $afterId
     *
     * @return EI[]|null
     */
    public function findBySyncStatusForGivenShop(array $statusArr, array $idShopArr, int $afterId = 0): ?array
    {
        try {
            if ($this instanceof AbandonedCartRepository) {
                return null;
            }

            $primary =  EI::T_PRIMARY_MAPPINGS[$this->entityMetaData->getTableName()];
            $conditions = [EI::C_ID_SHOP => $idShopArr, $primary => $afterId];
            if (! empty($statusArr)) {
                $conditions = array_merge($conditions, [EI::C_SYNC_STATUS => $statusArr]);
            }
            $sql = $this->buildSqlQuery($this->buildWhereClause($conditions), self::QUERY_LIMIT);
            return $this->hydrateMany($this->db->select(str_replace($primary . " =", $primary . " >", $sql)));
        } catch (Throwable $e) {
            $this->logger->logErrorMsg(__METHOD__, $e);
            return null;
        }
    }

    /**
     * @param array $statusArr
     * @param array $idShopArr
     * @param int $afterId
     *
     * @return int|null
     */
    public function getTotalCountBySyncStatusAndShop(array $statusArr, array $idShopArr, int $afterId): ?int
    {
        try {
            if ($this instanceof AbandonedCartRepository) {
                return null;
            }

            $pCol =  EI::T_PRIMARY_MAPPINGS[$this->entityMetaData->getTableName()];
            $condArr = [EI::C_ID_SHOP => $idShopArr];
            if (! empty($statusArr)) {
                $condArr = array_merge($condArr, [EI::C_SYNC_STATUS => $statusArr]);
            }

            if ($afterId > 0) {
                $condArr[$pCol] = $afterId;
            }

            $sql = sprintf(
                'SELECT COUNT(*) FROM %s WHERE %s', $this->getTableNameWithPrefix(), $this->buildWhereClause($condArr)
            );
            return EntityHelper::fetchSingleValueFromRow(str_replace($pCol . " =", $pCol . " >", $sql), 'integer');
        } catch (Throwable $e) {
            $this->logger->logErrorMsg(__METHOD__, $e);
            return null;
        }
    }
}
