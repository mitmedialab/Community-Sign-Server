<?php

/**
 * Call this (from cron) to clear all the caches
 * > cake clear_cache
 * @author rahulb
 *
 */
class ClearCacheShell extends Shell {
    
    function main() {
         Cache::clear();
         Cache::clear(false,'calendars');
         Cache::clear(false,'predictions');
         clearCache();
         $this->out("Cleared all the caches.");
    }
}

?>