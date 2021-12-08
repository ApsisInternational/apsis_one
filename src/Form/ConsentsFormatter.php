<?php

namespace Apsis\One\Form;

use FormField;
use FormFormatterInterface;

class ConsentsFormatter implements FormFormatterInterface
{
    /**
     * @var array
     */
    protected $topics = [];

    /**
     * @return array
     */
    public function getFormat(): array
    {
        $format = [];

        foreach ($this->topics as $topicArr) {
            if (! isset($topicArr['name']) || ! isset($topicArr['label'])) {
                continue;
            }

            $format[$topicArr['name']] = (new FormField())
                ->setName($topicArr['name'])
                ->setType('checkbox')
                ->setLabel($topicArr['label'])
                ->setValue(true);
        }

        return $format;
    }

    /**
     * @param array $topics
     *
     * @return $this
     */
    public function setTopics(array $topics): ConsentsFormatter
    {
        $this->topics = $topics;

        return $this;
    }
}