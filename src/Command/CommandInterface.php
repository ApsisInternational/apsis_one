<?php

namespace Apsis\One\Command;

interface CommandInterface
{
    const ARG_REQ_JOB = 'jobCode';
    const ARG_REQ_TEXT = 'Which operations job do you want to run?';

    // Sync command
    const COMMAND_NAME_SYNC = 'apsis-one:sync';
    const COMMAND_DESC_SYNC = 'Run APSIS sync operations.';
    const COMMAND_TEXT_SYNC = 'This commands allows you to ' . self::COMMAND_DESC_SYNC;
    const COMMAND_HELP_DESC_SYNC = self::COMMAND_TEXT_SYNC . ' ' . self::PROFILE_DESC . ' ' . self::EVENT_DESC;
    const ARG_REQ_DESC_SYNC = self::ARG_REQ_TEXT . " '" . self::JOB_TYPE_PROFILE . "' / '" . self::JOB_TYPE_EVENT . "'";
    const MSG_PROCESSOR_SYNC = ['=====================', 'APSIS Sync Operations', '=====================', ''];
    const JOB_TYPE_PROFILE = 'sync-profiles';
    const JOB_TYPE_EVENT = 'sync-events';
    const PROFILE_DESC = "Sync operation '" . self::JOB_TYPE_PROFILE . "' to sync Profiles.";
    const EVENT_DESC = "Sync operation '" . self::JOB_TYPE_EVENT . "' to sync Profile Events.";

    // Db command
    const COMMAND_NAME_DB = 'apsis-one:db';
    const COMMAND_DESC_DB = 'Run APSIS database operations.';
    const COMMAND_TEXT_DB = 'This commands allows you to ' . self::COMMAND_DESC_DB;
    const COMMAND_HELP_DESC_DB = self::COMMAND_TEXT_DB . ' ' . self::AC_DESC . ' ' . self::SUBS_UPDATE_DESC . ' ' .
        self::MISSING_PROFILES_DESC;
    const ARG_REQ_DESC_DB = self::ARG_REQ_TEXT . " '" . self::JOB_TYPE_SCAN_SUBS_UPDATE . "' / '" .
        self::JOB_TYPE_SCAN_AC . "'" . "' / '" . self::JOB_TYPE_SCAN_MISSING_PROFILES . "'";
    const MSG_PROCESSOR_DB = ['===================', 'APSIS DB Operations', '===================', ''];
    const JOB_TYPE_SCAN_AC = 'scan-abandoned-carts';
    const JOB_TYPE_SCAN_MISSING_PROFILES = 'scan-missing-profiles';
    const JOB_TYPE_SCAN_SUBS_UPDATE = 'scan-subs-updates';
    const AC_DESC = "Database operation '" . self::JOB_TYPE_SCAN_AC . "' to find abandoned carts.";
    const MISSING_PROFILES_DESC = "Database operation '" . self::JOB_TYPE_SCAN_MISSING_PROFILES .
        "' to find missing Profiles.";
    const SUBS_UPDATE_DESC = "Database operation '" . self::JOB_TYPE_SCAN_SUBS_UPDATE .
        "' to find missing subscription updates.";

    // Messages
    const MSG_SUCCESS = 'Successfully executed job with jobCode: %s. %s';
    const MSG_INVALID_JOB = 'Error. Invalid job with jobCode: %s.';
    const MSG_RUNTIME_ERR = "Error thrown during execution with jobCode: %s.\nError %s";
    const MSG_ALREADY_RUNNING = 'The command %s is already running in another process.';

    const KEY_UPDATE_TYPE = 'update_type';
    const SYNC_UPDATE_TYPE_PROFILE = 'profile';
    const SYNC_UPDATE_TYPE_EVENT = 'event';
}
