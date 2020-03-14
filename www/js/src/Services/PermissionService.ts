import { IPermission, IPermissionContainer } from "../Model/Permission";
import { url, defaultConfig } from "../Helpers/ApiHelper";

export interface IPermissionService {
	getUserPermissionByOrganizationId(
		organization_id: string
	): angular.IPromise<IPermission>;
}

export const PermissionServiceFactory = function(
	$http: angular.IHttpService
): IPermissionService {
	return {
		getUserPermissionByOrganizationId(
			organization_id: string
		): angular.IPromise<IPermission> {
			return $http
				.get<IPermissionContainer>(
					url("organizations", organization_id, "role"),
					defaultConfig
				)
				.then(success => success.data.permission);
		}
	};
};
