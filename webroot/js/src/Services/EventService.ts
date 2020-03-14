import { url, defaultConfig } from "../Helpers/ApiHelper";
import { IRsvpService } from "./RsvpService";
import { IOrganizationService } from "./OrganizationService";
import { ITimeService } from "./TimeService";
import { ICommentService } from "./CommentService";
import { IAddressService } from "./AddressService";
import { ISkillService } from "./SkillService";

import { IEventContainer, IEvent, IRecommendedEventContainer, IHasManyEvent } from "@models/Event";
import { IHasOneSkill } from "@models/Skill";

export interface IEventService {
	getRecommended(): angular.IPromise<Array<IHasManyEvent | IHasOneSkill>>;
	getByEventId(event_id: string): angular.IPromise<IEvent>;
}

export const EventServiceFactory = function(
	$http: angular.IHttpService,
	$q: angular.IQService,
	OrganizationService: IOrganizationService,
	RsvpService: IRsvpService,
	TimeService: ITimeService,
	CommentService: ICommentService,
	AddressService: IAddressService,
	SkillService: ISkillService
): IEventService {
	return {
		getRecommended: function() {
			return $http.get<IRecommendedEventContainer>(
				url("events", "recommended"),
				defaultConfig
			).then(success => success.data.recommended);
		},
		getByEventId: function(event_id: string) {
			return $http
				.get<IEventContainer>(url("events", event_id), defaultConfig)
				.then(success => {
					let event = success.data.event;

					// Attach lazy-load methods

					event.Organization = function() {
						return OrganizationService.getByOrganizationId(event.organization_id);
					};

					event.Addresses = function() {
						return AddressService.getByEventId(event.event_id);
					};

					event.Comments = function() {
						return CommentService.getThreadByEventId(event.event_id);
					};

					event.Rsvps = function() {
						return RsvpService.getAllByEventId(event.event_id);
					};

					event.Skills = function() {
						return SkillService.getByEventId(event.event_id);
					};

					event.Times = function() {
						return TimeService.getAllByEventId(event.event_id);
					};

					return event;
				});
		}
	};
};
