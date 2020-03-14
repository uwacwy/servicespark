import { IOrganization, IOrganizationContainer } from "../Model/Organization";
import { url, defaultConfig } from "../Helpers/ApiHelper";
import { IPermissionService } from "./PermissionService";

export interface IOrganizationService {
	getByOrganizationId(organization_id: string): angular.IPromise<IOrganization>;
}

export const OrganizationServiceFactory = function(
    $http: angular.IHttpService,
    PermissionService: IPermissionService
): IOrganizationService {
    return {
        getByOrganizationId: function(
            organization_id: string
        ): angular.IPromise<IOrganization> {
            return $http
                .get<IOrganizationContainer>(
                    url("organizations", organization_id),
                    defaultConfig
                )
                .then(success => {
                    let organization = success.data.organization;

                    organization.Role = function() {
                        return PermissionService.getUserPermissionByOrganizationId(
                            organization.organization_id
                        );
                    };

                    return organization;
                });
        }
    };
}