import { IPermission } from "./Permission";
import { IEvent } from "./Event";

export interface IOrganization {
	// Primary Key
	organization_id?: string;

	// Lazy Relationships
	Role: () => angular.IPromise<IPermission>;
	Permission?: IPermission[];
	Event?: IEvent[];

	// Data
	name?: string;
	description?: string;

	// Timestamps
	created?: string;
	modified?: string;
}



export interface IOrganizationContainer {
	organization: IOrganization;
}