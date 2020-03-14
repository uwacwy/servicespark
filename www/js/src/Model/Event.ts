import { IPermission } from "./Permission";
import { IOrganization } from "./Organization";
import { IAddress } from "./Address";
import { ICommentParent } from "./Comment";
import { IRsvp } from "./Rsvp";
import { ISkill, IHasOneSkill } from "./Skill";
import { ITime } from "./Time";

export interface IRecommendedEventContainer {
	// an array of objects with Skill: ISkill and and Event: IEvent[]
	recommended: Array<IHasOneSkill | IHasManyEvent>
}

export interface IHasManyEvent {
	Event: IEvent[]
}
export interface IEventContainer {
	event: IEvent;
}

export interface IEvent {
	// Primary Key
	event_id?: string;

	// Foreign Keys
	organization_id?: string;

	// Relationships
	Permission?: IPermission[];
	Event?: IEvent[];

	// Data
	title?: string;
	description?: string;
	start_time?: string;
	stop_time?: string;
	rsvp_desired?: string;
	start_token?: string;
	stop_token?: string;

	// Summary statistics
	comment_count?: string;
	missed_punches?: string;
	rsvp_count?: string;
	rsvp_maybe?: string;
	rsvp_not_going?: string;

	// Calculated
	rsvp_percent?: string;

	// Lazy
	Organization: () => angular.IPromise<IOrganization>;

	Addresses: () => angular.IPromise<IAddress[]>;
	Comments: () => angular.IPromise<ICommentParent[]>;
	Rsvps: () => angular.IPromise<IRsvp[]>;
	Skills: () => angular.IPromise<ISkill[]>;
	Times: () => angular.IPromise<ITime[]>;

	Role: () => angular.IPromise<IPermission>;
}
