import { url } from "../Helpers/ApiHelper";
import { ISkill, ISkillContainer } from "../Model/Skill";

export interface ISkillService {
	getByEventId(event_id: string): angular.IPromise<ISkill[]>;
}

export const SkillServiceFactory = function(
	$http: angular.IHttpService,
	$q: angular.IQService
): ISkillService {
	return {
		getByEventId(event_id: string): angular.IPromise<ISkill[]> {
			return $http
				.get<ISkillContainer>(url("events", event_id, "skills"), {
					headers: {
						Accept: "application/json"
					}
				})
				.then(success => success.data.skills);
		}
	};
};
