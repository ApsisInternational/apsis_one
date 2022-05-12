<?php

namespace Apsis\One\Command;

interface CommandInterface
{
    const COMMAND_DESC_SYNC = 'Run APSIS sync operations, Profiles and Events.';
    const COMMAND_DESC_DB = 'Run APSIS database operations.';
    const ARG_REQ_JOB = 'jobCode';
    const JOB_TYPE_PROFILE = 'sync-profiles';
    const JOB_TYPE_EVENT = 'sync-events';
    const JOB_TYPE_SCAN_AC = 'scan-abandoned-carts';
    const JOB_TYPE_SCAN_MISSING = 'scan-missing';
    const JOB_TYPE_SCAN_MISSING_PROFILES = 'scan-missing-profiles';
    const JOB_TYPE_SCAN_SUBS_UPDATE = 'scan-subs-updates';
}
