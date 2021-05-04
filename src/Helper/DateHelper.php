<?php

namespace Apsis\One\Helper;

use DateTime;
use DateTimeZone;
use Exception;
use DateInterval;

class DateHelper
{
    const TIMESTAMP = 'U';
    const ISO_8601 = 'c';

    /**
     * @var LoggerHelper
     */
    protected $loggerHelper;

    /**
     * DateHelper constructor.
     *
     * @param LoggerHelper $loggerHelper
     */
    public function __construct(LoggerHelper $loggerHelper)
    {
        $this->loggerHelper = $loggerHelper;
    }

    /**
     * @param string $date
     * @param string $format
     *
     * @return string
     */
    public function formatDateForPlatformCompatibility($date = null, $format = self::TIMESTAMP)
    {
        try {
            if (empty($date)) {
                $date = 'now';
            }

            return $this->getDateTimeFromTime($date)->format($format);
        } catch (Exception $e) {
            $this->loggerHelper->logErrorToFile(__METHOD__, $e->getMessage(), $e->getTraceAsString());
            return '';
        }
    }

    /**
     * @param string $date
     * @param string $format
     *
     * @return string
     */
    public function addSecond($date = null, $format = self::TIMESTAMP)
    {
        try {
            if (empty($date)) {
                $date = 'now';
            }

            return $this->getDateTimeFromTime($date)
                ->add($this->getDateIntervalFromIntervalSpec('PT1S'))
                ->format($format);
        } catch (Exception $e) {
            $this->loggerHelper->logErrorToFile(__METHOD__, $e->getMessage(), $e->getTraceAsString());
            return '';
        }
    }

    /**
     * @param string $inputDateTime
     * @param int $day
     *
     * @return string
     */
    public function getFormattedDateTimeWithAddedInterval(string $inputDateTime, int $day = 1)
    {
        try {
            return $this->getDateTimeFromTimeAndTimeZone($inputDateTime)
                ->add($this->getDateIntervalFromIntervalSpec(sprintf('P%sD', $day)))
                ->format(self::ISO_8601);
        } catch (Exception $e) {
            $this->loggerHelper->logErrorToFile(__METHOD__, $e->getMessage(), $e->getTraceAsString());
            return '';
        }
    }

    /**
     * @param string $inputDateTime
     *
     * @return bool
     */
    public function isExpired(string $inputDateTime)
    {
        try {
            $nowDateTime = $this->getDateTimeFromTimeAndTimeZone()->format(self::ISO_8601);
            return ($nowDateTime > $inputDateTime);
        } catch (Exception $e) {
            $this->loggerHelper->logErrorToFile(__METHOD__, $e->getMessage(), $e->getTraceAsString());
            return false;
        }
    }

    /**
     * @param string $time
     * @param string $timezone
     *
     * @return DateTime
     *
     * @throws Exception
     */
    public function getDateTimeFromTimeAndTimeZone($time = 'now', $timezone = 'UTC')
    {
        return new DateTime($time, new DateTimeZone($timezone));
    }

    /**
     * @param string $time
     *
     * @return DateTime
     *
     * @throws Exception
     */
    public function getDateTimeFromTime($time = 'now')
    {
        return new DateTime($time);
    }

    /**
     * @param string $intervalSpec
     *
     * @return DateInterval
     *
     * @throws Exception
     */
    public function getDateIntervalFromIntervalSpec(string $intervalSpec)
    {
        return new DateInterval($intervalSpec);
    }
}