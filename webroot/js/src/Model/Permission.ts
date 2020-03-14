export interface IPermission {
	// Primary Key
	permission_id?: string;

	// Foreign Keys
	organization_id?: string;
	user_id?: string;

	// Data
	publish?: boolean;
	read?: boolean;
	write?: boolean;

	// Dates
	created?: string;
	modified?: string;
}

export interface IPermissionContainer {
	permission: IPermission;
}