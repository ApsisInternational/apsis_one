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
    const JOB_TYPE_PROFILE = 'profile';
    const JOB_TYPE_EVENT = 'event';
    const PROFILE_DESC = "Sync operation '" . self::JOB_TYPE_PROFILE . "' to sync Profiles.";
    const EVENT_DESC = "Sync operation '" . self::JOB_TYPE_EVENT . "' to sync Profile Events.";

    // Db command
    const COMMAND_NAME_DB = 'apsis-one:db';
    const COMMAND_DESC_DB = 'Run APSIS database operations.';
    const COMMAND_TEXT_DB = 'This commands allows you to ' . self::COMMAND_DESC_DB;
    const COMMAND_HELP_DESC_DB = self::COMMAND_TEXT_DB . ' ' . self::CLEANUP_DESC . ' ' . self::AC_DESC . ' ' . self::SUBS_UPDATE_DESC;
    const ARG_REQ_DESC_DB = self::ARG_REQ_TEXT . " '" . self::JOB_TYPE_CLEANUP . "' / '" . self::JOB_TYPE_AC . "'";
    const MSG_PROCESSOR_DB = ['===================', 'APSIS DB Operations', '===================', ''];
    const JOB_TYPE_CLEANUP = 'cleanup';
    const JOB_TYPE_AC = 'abandoned-carts';
    const JOB_TYPE_SCAN_SUBS_UPDATE = 'scan-subs-updates';
    const CLEANUP_DESC = "Database operation '" . self::JOB_TYPE_CLEANUP . "' to cleanup APSIS tables.";
    const AC_DESC = "Database operation '" . self::JOB_TYPE_AC . "' to find abandoned carts.";
    const SUBS_UPDATE_DESC = "Database operation '" . self::JOB_TYPE_SCAN_SUBS_UPDATE . "' to find missing subscription updates.";

    // Messages
    const MSG_SUCCESS = '<info>Successfully executed job with jobCode: %s.</info>';
    const MSG_ERROR = '<error>Error. Invalid job with jobCode: %s.</error>';
    const MSG_ALREADY_RUNNING = '<info>The command %s is already running in another process.</info>';
}
