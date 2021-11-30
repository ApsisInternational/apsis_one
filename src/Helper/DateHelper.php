<?php

namespace Apsis\One\Helper;

use Configuration;
use DateTime;
use DateTimeZone;
use Throwable;
use DateInterval;

class DateHelper extends LoggerHelper
{
    /**
     * @param string $format
     * @param string|null $date
     * @param int|null $idShop
     * @param string|null $oPtz
     *
     * @return int|string|null
     */
    public function formatDate(string $format, ?string $date = null, ?int $idShop = null, ?string $oPtz = null)
    {
        try {
            if (empty($date)) {
                $date = self::DT_NOW;
            }

            $dateTime = $this->getDateTimeFromTimeAndTimeZone($date, $this->getShopsTimezone(null, $idShop));

            if (! empty($oPtz)) {
                $dateTime->setTimezone(new DateTimeZone($oPtz));
            }

            $formattedDateTime = $dateTime->format($format);
            return $format === self::TIMESTAMP ? (int) $formattedDateTime : $formattedDateTime;
        } catch (Throwable $e) {
            $this->logErrorMsg(__METHOD__, $e);
            return $format === self::TIMESTAMP ? 0 : '';
        }
    }

    /**
     * @param string|null $date
     * @param string $format
     *
     * @return string|int
     */
    public function addSecond(?string $date = null, string $format = self::TIMESTAMP)
    {
        try {
            if (empty($date)) {
                $date = self::DT_NOW;
            }

            $formattedDateTime = $this->getDateTimeFromTime($date)
                ->add($this->getDateIntervalFromIntervalSpec('PT1S'))
                ->format($format);
            return $format === self::TIMESTAMP ? (int) $formattedDateTime : $formattedDateTime;
        } catch (Throwable $e) {
            $this->logErrorMsg(__METHOD__, $e);
            return '';
        }
    }

    /**
     * @param string $time
     * @param string $timezone
     *
     * @return DateTime
     *
     * @throws Throwable
     */
    public function getDateTimeFromTimeAndTimeZone(string $time = self::DT_NOW, string $timezone = self::TZ_UTC): DateTime
    {
        return new DateTime($time, new DateTimeZone($timezone));
    }

    /**
     * @param string $time
     *
     * @return DateTime
     *
     * @throws Throwable
     */
    public function getDateTimeFromTime(string $time = self::DT_NOW): DateTime
    {
        return new DateTime($time);
    }

    /**
     * @param string $datetime
     * @param int|null $idShopGroup
     * @param int|null $idShop
     * @param string $format
     *
     * @return string|null
     *
     * @throws Throwable
     */
    public function convertDatetimeToShopsTimezoneAndFormat(
        string $datetime,
        ?int $idShopGroup = null,
        ?int $idShop = null,
        string $format = 'Y-m-d H:i:s'
    ): ?string {
        if (empty($datetime) || empty($format)) {
            return null;
        }

        return $this->getDateTimeFromTime($datetime)
            ->setTimezone(new DateTimeZone($this->getShopsTimezone($idShopGroup, $idShop)))
            ->format($format);
    }

    /**
     * @param int|null $idShopGroup
     * @param int|null $idShop
     *
     * @return string
     */
    public function getShopsTimezone(?int $idShopGroup, ?int $idShop): string
    {
        $default = 'Europe/Stockholm';
        return (string) Configuration::get('PS_TIMEZONE', null, $idShopGroup, $idShop, $default);
    }

    /**
     * @param string $intervalSpec
     *
     * @return DateInterval
     *
     * @throws Throwable
     */
    public function getDateIntervalFromIntervalSpec(string $intervalSpec): DateInterval
    {
        return new DateInterval($intervalSpec);
    }
}
