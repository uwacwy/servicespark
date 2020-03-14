export interface IRsvp {
	// Primary Key
	rsvp_id?: string;

	// Foreign Keys
	event_id?: string;
	user_id?: string;

	// Data
	status?: "going" | "not_going" | "maybe";

	// Timestamps
	created?: string;
	modified?: string;
}

export interface IRsvpContainer {
	rsvp: IRsvp;
}

export interface IRsvpsContainer {
	rsvps: IRsvp[];
}
