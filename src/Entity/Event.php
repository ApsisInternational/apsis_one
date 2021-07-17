<?php

namespace Apsis\One\Entity;

use Apsis\One\Entity\Repository\EventRepository;
use Apsis\One\Entity\Collection\EventCollection;
use Shop;

class Event extends AbstractEntity
{
    /**
     * @var int
     */
    protected $id_apsis_event;

    /**
     * @var int
     */
    protected $event_type;

    /**
     * @var string
     */
    protected $event_data;

    /**
     * @var string
     */
    protected $sub_event_data = self::EMPTY;

    /**
     * @var string
     */
    protected $date_add;

    /**
     * {@inheritdoc}
     */
    public static $definition = [
        'table' => self::T_EVENT,
        'primary' => self::T_PRIMARY_MAPPINGS[self::T_EVENT],
        'fields' => self::T_COLUMNS_MAPPINGS[self::T_EVENT],
        'associations' => [
            'shop' => [
                'type' => self::HAS_ONE,
                'field' => self::C_ID_SHOP,
                'object' => Shop::class,
                'association' => 'shop'
            ],
            'profile' => [
                'type' => self::HAS_ONE,
                'field' => self::C_ID_PROFILE,
                'object' => Profile::class,
                'association' => self::T_PROFILE
            ],
        ]
    ];

    /**
     * {@inheritdoc}
     */
    public static function fetchCollectionClassName(): string
    {
        return EventCollection::class;
    }

    /**
     * {@inheritdoc}
     */
    public static function fetchRepositoryClassName(): string
    {
        return EventRepository::class;
    }

    /**
     * @return int
     */
    public function getIdApsisEvent(): int
    {
        return $this->id_apsis_event;
    }

    /**
     * @param int $id
     *
     * @return $this
     */
    public function setIdApsisEvent(int $id): Event
    {
        $this->id_apsis_event = $id;
        return $this;
    }

    /**
     * @return int
     */
    public function getEventType(): int
    {
        return $this->event_type;
    }

    /**
     * @param int $eventType
     *
     * @return $this
     */
    public function setEventType(int $eventType): Event
    {
        $this->event_type = $eventType;
        return $this;
    }

    /**
     * @return string
     */
    public function getEventData(): string
    {
        return $this->event_data;
    }

    /**
     * @param string $data
     *
     * @return $this
     */
    public function setEventData(string $data): Event
    {
        $this->event_data = $data;
        return $this;
    }

    /**
     * @return string
     */
    public function getSubEventData(): string
    {
        return $this->sub_event_data;
    }

    /**
     * @param string $data
     *
     * @return $this
     */
    public function setSubEventData(string $data = self::EMPTY): Event
    {
        $this->sub_event_data = $data;
        return $this;
    }

    /**
     * @return string
     */
    public function getDateAdd(): string
    {
        return $this->date_add;
    }

    /**
     * @param string $date
     *
     * @return $this
     */
    public function setDateAdd(string $date): Event
    {
        $this->date_add = $date;
        return $this;
    }
}