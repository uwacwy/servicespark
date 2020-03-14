import { IUser } from "./User";

export interface ITime {
	time_id?: string;
	user_id?: string;

	// Data
	status?: string;
	start_time?: string;
	stop_time?: string;

	// Computed
	duration?: string;

	// Timestamps
	created?: string;
	modified?: string;

	// Lazy Relations
	User: () => angular.IPromise<IUser>;
}

export interface ITimesContainer {
	times: ITime[];
}