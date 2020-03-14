import { url } from "../Helpers/ApiHelper";
import { ITime, ITimesContainer } from "../Model/Time";

export interface ITimeService {
	getAllByEventId(event_id: string): angular.IPromise<ITime[]>;
}

export const TimeServiceFactory = function(
	$http: angular.IHttpService,
	$q: angular.IQService
): ITimeService {
	return {
		getAllByEventId(event_id: string): angular.IPromise<ITime[]> {
			return $http
				.get<ITimesContainer>(url("events", event_id, "times"), {
					headers: {
						Accept: "application/json"
					}
				})
				.then(success => success.data.times);
		}
	};
};
