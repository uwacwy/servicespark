Hello, *|full_name|*!

A community service opportunity is coming up.

*|event_title|* is hosted by *|organization_name|*
from *|event_start_time|* until *|event_stop_time|*

<?php if( $event['Event']['rsvp_count'] < $event['Event']['rsvp_desired'] ) : ?>
*|event_title|* still needs about <?php echo ($event['Event']['rsvp_desired'] - $event['Event']['rsvp_count']); ?> volunteers.
<?php endif; ?>

To quickly let *|organization_name|* know you're coming, click this link:
*|event_going_link|*

To view more information about this event, and communicate with other volunteers, visit the event page:
*|event_url|*

You are receiving this email because...
*|why_this_email|*