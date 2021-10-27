<?php

namespace Apsis\One\Model;

use Apsis\One\Repository\EventRepository;

class Event extends AbstractEntity
{
    /**
     * @var int
     */
    public $id_apsis_event;

    /**
     * @var int
     */
    public $id_entity_ps;

    /**
     * @var int
     */
    public $event_type;

    /**
     * @var string
     */
    public $event_data;

    /**
     * @var string
     */
    public $date_add;

    /**
     * {@inheritdoc}
     */
    public static $definition = [
        'table' => self::T_EVENT,
        'primary' => self::T_PRIMARY_MAPPINGS[self::T_EVENT],
        'fields' => self::T_COLUMNS_MAPPINGS[self::T_EVENT]
    ];

    /**
     * @inheritDoc
     */
    public static function getRepositoryClassName(): string
    {
        return EventRepository::class;
    }

    /**
     * @return int
     */
    public function getIdApsisEvent(): int
    {
        return (int) $this->id_apsis_event;
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
    public function getIdEntityPs(): int
    {
        return (int) $this->id_entity_ps;
    }

    /**
     * @param int $idEntityPs
     *
     * @return $this
     */
    public function setIdEntityPs(int $idEntityPs): AbstractEntity
    {
        $this->id_entity_ps = $idEntityPs;
        return $this;
    }

    /**
     * @return int
     */
    public function getEventType(): int
    {
        return (int) $this->event_type;
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
        return (string) $this->event_data;
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
    public function getDateAdd(): string
    {
        return (string) $this->date_add;
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
