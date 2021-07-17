<?php

namespace Apsis\One\Helper;

use PrestaShop\PrestaShop\Core\Foundation\Database\EntityManager;
use Apsis\One\Entity\Repository\RepositoryInterface;
use Apsis\One\Entity\Collection\CollectionInterface;
use Apsis\One\Entity\Profile;
use Apsis\One\Entity\Event;
use Apsis\One\Entity\AbandonedCart;
use Exception;
use Throwable;


class EntityHelper extends LoggerHelper
{
    const VALID_CLASSES = [Profile::class, Event::class, AbandonedCart::class];
    const ENTITY_METHOD_COLLECTION = 'getCollectionClassName';

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * EntityHelper constructor.
     *
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
        parent::__construct();
    }

    /**
     * @param string $entityClass
     * @return RepositoryInterface
     *
     * @throws Throwable
     */
    public function getRepository(string $entityClass): RepositoryInterface
    {
        $this->assertClassIsValid($entityClass);
        return $this->entityManager->getRepository($entityClass);
    }

    /**
     * @param string $entityClass
     * @return CollectionInterface
     *
     * @throws Throwable
     */
    public function getCollection(string $entityClass): CollectionInterface
    {
        $this->assertClassIsValid($entityClass);
        $this->assertCollectionNameExist($entityClass);
        $collectionClassName = call_user_func([$entityClass, self::ENTITY_METHOD_COLLECTION]);
        return new $collectionClassName();
    }

    /**
     * @param string $entityClass
     *
     * @throws Throwable
     */
    private function assertClassIsValid(string $entityClass)
    {
        if (! in_array($entityClass, self::VALID_CLASSES)) {
            throw new Exception('Invalid class');
        }
    }

    /**
     * @param string $entityClass
     *
     * @throws Throwable
     */
    private function assertCollectionNameExist(string $entityClass)
    {
        if (! is_callable([$entityClass, self::ENTITY_METHOD_COLLECTION])) {
            throw new Exception('Collection class is not set for entity');
        }
    }

    /**
     * VALID RFC 4211 COMPLIANT Universally Unique Identifier (UUID) version 4
     * https://www.php.net/manual/en/function.uniqid.php#94959
     *
     * @return string
     */
    public static function generateUniversallyUniqueIdentifier(): string
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',

            // 32 bits for "time_low"
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),

            // 16 bits for "time_mid"
            mt_rand(0, 0xffff),

            // 16 bits for "time_hi_and_version",
            // four most significant bits holds version number 4
            mt_rand(0, 0x0fff) | 0x4000,

            // 16 bits, 8 bits for "clk_seq_hi_res",
            // 8 bits for "clk_seq_low",
            // two most significant bits holds zero and one for variant DCE1.1
            mt_rand(0, 0x3fff) | 0x8000,

            // 48 bits for "node"
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff)
        );
    }
}