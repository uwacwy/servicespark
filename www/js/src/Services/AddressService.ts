import { IAddress, IAddressContainer } from "../Model/Address";
import { url, defaultConfig } from "../Helpers/ApiHelper";

export interface IAddressService {
	getByEventId(event_id: string): angular.IPromise<IAddress[]>;

	save(address: IAddress): angular.IPromise<IAddress>;
	update(address: IAddress): angular.IPromise<IAddress>;
	delete(address_id: string ): angular.IPromise<any>;
}

export const AddressServiceFactory = function(
	$http: angular.IHttpService,
	$q: angular.IQService
): IAddressService {
	return {
		save(address: IAddress){
			return $q.when(address);
		},
		update(address: IAddress) {
			return $q.when(address);
		},
		delete(address_id: string ) {
			return $http.delete(
				url("addresses", address_id),
				defaultConfig
			)
		},
		getByEventId(event_id: string): angular.IPromise<IAddress[]> {
			return $http
				.get<IAddressContainer>(
					url("events", event_id, "addresses"), 
					defaultConfig
				)
				.then(success => success.data.addresses);
		}
	};
};
