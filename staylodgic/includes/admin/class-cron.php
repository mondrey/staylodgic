<?php

namespace Staylodgic;

class Cron
{

    public function __construct() {

        add_filter('cron_schedules',  array($this, 'add_cron_intervals'));
        $this->cron_initialize();
    }

    public function get_scheduled_time() {
        $qtysync_interval = null; // or set a default value
        $qtysync_interval = get_blog_option(get_current_blog_id(), 'site_sync_interval');
    
        // Define the cron schedule based on the validated interval
        switch ($qtysync_interval) {
            case '1':
                $schedule = 'staylodgic_1_minutes';
                break;
            case '5':
                $schedule = 'staylodgic_5_minutes';
                break;
            case '10':
                $schedule = 'staylodgic_10_minutes';
                break;
            case '15':
                $schedule = 'staylodgic_15_minutes';
                break;
            case '30':
                $schedule = 'staylodgic_30_minutes';
                break;
            case '60':
                $schedule = 'staylodgic_60_minutes';
                break;
            default:
                $schedule = 'staylodgic_30_minutes';
        }
    
        error_log('Sync ' . $qtysync_interval );
        error_log('Sync schedule ' . $schedule );
    
        return $schedule;
    }
    
    
    public function cron_initialize() {
        error_log('Activating Staylodgic Plugin.');
        
        $current_meta_schedule = get_option('current_ical_processor_schedule');
        $new_schedule = $this->get_scheduled_time();
    
        error_log('Current Schedule from Metadata: ' . $current_meta_schedule);
        error_log('New Schedule from Settings: ' . $new_schedule);
        
        if ($current_meta_schedule !== $new_schedule) {
            $scheduled_time = wp_next_scheduled('staylodgic_ical_availability_processor_event');
            
            if ($scheduled_time) {
                error_log('Attempting to unschedule at timestamp: ' . $scheduled_time);
                $unschedule_result = wp_unschedule_event($scheduled_time, 'staylodgic_ical_availability_processor_event');
                error_log('Unschedule result: ' . ($unschedule_result ? 'Success' : 'Failed'));
            }
            
            $reschedule_result = wp_schedule_event(time(), $new_schedule, 'staylodgic_ical_availability_processor_event');
            error_log('Attempting to reschedule: ' . $new_schedule);
            error_log('Reschedule result: ' . ($reschedule_result ? 'Success' : 'Failed'));
    
            update_option('current_ical_processor_schedule', $new_schedule);
        } else {
            error_log('No rescheduling needed. Current schedule matches the new schedule.');
        }
    
        // Schedule the cron event if it's not already scheduled
        if (!wp_next_scheduled('staylodgic_ical_availability_processor_event')) {
            $fresh_schedule_result = wp_schedule_event(time(), $new_schedule, 'staylodgic_ical_availability_processor_event');
            error_log('Attempting to set a fresh schedule: ' . $fresh_schedule_result);
            error_log('Fresh schedule result: ' . ($fresh_schedule_result ? 'Success' : 'Failed'));
        }
    
        $cron_jobs = _get_cron_array(); // Retrieve the cron array
        // error_log('Current cron jobs: ' . print_r($cron_jobs, true)); // Log it to see all scheduled cron jobs
    }
    
    public function add_cron_intervals($schedules) {
        $sync_intervals = array(
            '1' => array('interval' => 60, 'display' => 'Every Minute'),
            '5' => array('interval' => 300, 'display' => 'Every 5 Minutes'),
            '10' => array('interval' => 600, 'display' => 'Every 10 Minutes'),
            '15' => array('interval' => 900, 'display' => 'Every 15 Minutes'),
            '30' => array('interval' => 1800, 'display' => 'Every 30 Minutes'),
            '60' => array('interval' => 3600, 'display' => 'Every Hour')
        );
    
        foreach ($sync_intervals as $key => $settings) {
            $schedules["staylodgic_{$key}_minutes"] = $settings;
        }
    
        return $schedules;
    }
}

$instance = new \Staylodgic\Cron();
