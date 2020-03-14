import * as angular from "angular";
import * as ui from "@uirouter/angularjs";
import * as showdown from "showdown";

// Factories
import { AddressServiceFactory, IAddressService } from "@services/AddressService";
import { CommentServiceFactory, ICommentService } from "@services/CommentService";
import {
	OrganizationServiceFactory,
	IOrganizationService
} from "@services/OrganizationService";
import {
	PermissionServiceFactory,
	IPermissionService
} from "@services/PermissionService";
import { SkillServiceFactory, ISkillService } from "@services/SkillService";
import { TimeServiceFactory, ITimeService } from "@services/TimeService";
import { RsvpServiceFactory, IRsvpService } from "@services/RsvpService";
import { EventServiceFactory, IEventService } from "@services/EventService";

// Directives
import { userRsvpDirectiveFactory } from "@directives/userRsvp";
import { commentFormDirectiveFactory } from "@directives/commentForm";
import { commentDisplayDirectiveFactory } from "@directives/commentDisplay";
import { eventCardFactory } from "@directives/eventCard";

import { IRsvp } from "@models/Rsvp";
import { IEvent } from "@models/Event";
import { IPermission } from "@models/Permission";
import { ITime } from "@models/Time";
import { IOrganization } from "@models/Organization";

let ServiceSpark_Times = angular
	.module("ServiceSpark.Times", [])
	.factory("TimeService", ["$http", "$q", TimeServiceFactory]);

let ServiceSpark_Permissions = angular
	.module("ServiceSpark.Permissions", [])
	.factory("PermissionService", ["$http", PermissionServiceFactory]);

let ServiceSpark_Organizations = angular
	.module("ServiceSpark.Organizations", [])
	.factory("OrganizationService", [
		"$http",
		"PermissionService",
		OrganizationServiceFactory
	]);

let ServiceSpark_Comments_Comment = angular
	.module("ServiceSpark.Comments.Comment", [])
	.directive("commentDisplay", [commentDisplayDirectiveFactory])
	.directive("commentForm", [
		"$timeout",
		"CommentService",
		commentFormDirectiveFactory
	]);

let ServiceSpark_Comments = angular
	.module("ServiceSpark.Comments", ["ServiceSpark.Comments.Comment"])
	.factory("CommentService", ["$http", "$q", CommentServiceFactory]);

let ServiceSpark_Addresses = angular
	.module("ServiceSpark.Addresses", [])
	.factory("AddressService", ["$http", "$q", AddressServiceFactory]);

let ServiceSpark_Skills = angular
	.module("ServiceSpark.Skills", [])
	.factory("SkillService", ["$http", "$q", SkillServiceFactory]);

// Controllers
import { DetailController } from "@controllers/Events/DetailController";
import { EditController } from "@controllers/Events/EditController";
import { RecommendedController } from "@controllers/Events/RecommendedController";

let ServiceSpark_Events_Event = angular
	.module("ServiceSpark.Events.Event", [])
	.controller("DetailController", DetailController)
	.controller("EditController", EditController)
	.controller("RecommendedController", RecommendedController)
	.directive("userRsvp", [userRsvpDirectiveFactory])
	.directive("eventCard", [eventCardFactory]);

let ServiceSpark_Events = angular
	.module("ServiceSpark.Events", ["ServiceSpark.Events.Event"])
	.factory("EventService", [
		"$http",
		"$q",
		"OrganizationService",
		"RsvpService",
		"TimeService",
		"CommentService",
		"AddressService",
		"SkillService",
		EventServiceFactory
	]);

let ServiceSpark_Rsvps = angular
	.module("ServiceSpark.Rsvps", [])
	.factory("RsvpService", ["$http", "$q", RsvpServiceFactory]);

import * as moment from "moment";

let ServiceSpark = angular
	.module("ServiceSparkApp", [
		"ui.router",
		"ServiceSpark.Events",
		"ServiceSpark.Rsvps",
		"ServiceSpark.Events",
		"ServiceSpark.Organizations",
		"ServiceSpark.Times",
		"ServiceSpark.Comments",
		"ServiceSpark.Addresses",
		"ServiceSpark.Skills",
		"ServiceSpark.Permissions"
	])
	.filter("parse", function() {
		return function(input: string, format?: string): moment.Moment {
			return moment(input, format);
		};
	})
	.filter("format", function() {
		return function(input: any, format: string) {
			if (moment.isMoment(input)) {
				return input.format(format);
			}
		};
	})
	.filter("toDate", function() {
		return function(input: any) {
			if (moment.isMoment(input)) {
				return input.toDate();
			}
		};
	})
	.filter("fromNow", function() {
		return function(input: moment.Moment) {
			if (moment.isMoment(input)) return input.fromNow();
			else {
				return moment(input).fromNow();
			}
		};
	})
	.filter("markdown", [
		"$sce",
		function($sce: angular.ISCEService) {
			console.log("Instantiating `markdown` filter with $sce.");
			let conv = new showdown.Converter({
				noHeaderId: true,
				headerLevelStart: 4
			});
			return function(input: string): string {
				return $sce.trustAsHtml(conv.makeHtml(input));
			};
		}
	])
	.config([
		"$stateProvider",
		"$urlRouterProvider",
		($stateProvider: ui.StateProvider, $urlProvider: ui.UrlRouterProvider) => {
			$urlProvider.otherwise("/events/recommended");

			$stateProvider
				/*
                    Organizations
                */
				.state({
					name: "organizations",
					url: "/organizations",
					template: "<ui-view></ui-view>"
				})
				.state({
					name: "organizations.organization",
					url: "/:organization_id",
					template: "<ui-view></ui-view>"
				})
				.state({
					name: "organizations.organization.detail",
					url: "/detail",
					template: "<h1>Organization Here!</h1>"
				})

				/*
                    Events
                 */
				.state({
					name: "events",
					url: "/events",
					template: "<ui-view></ui-view>",
					controller: function() {
						console.log("Events", arguments);
					}
				})
				.state({
					name: "events.recommended",
					url: "/recommended",
					templateUrl: "/templates/events/recommended.html",
					controller: "RecommendedController",
					controllerAs: "recommendedCtrl",
					resolve: {
						recommended: [
							"EventService",
							(EventService: IEventService) => EventService.getRecommended()
						]
					}
				})
				.state({
					name: "events.event",
					url: "/:eventId",
					template: `<ui-view></ui-view>`,
					controller: [
						"$scope",
						"RsvpService",
						"event_rsvps",
						(
							$scope: angular.IScope,
							RsvpService: IRsvpService,
							event_rsvps: IRsvp[]
						) => {
							$scope.$on(
								"user_rsvp_updated",
								(evt: angular.IAngularEvent, user_rsvp: IRsvp) => {
									RsvpService.updateRsvp(user_rsvp)
										.then(updatedRsvp =>
											RsvpService.getAllByEventId(updatedRsvp.event_id)
										)
										.then(updatedRsvps => {
											event_rsvps = updatedRsvps;
										});
								}
							);
						}
					],
					resolve: {
						event: [
							"EventService",
							"$stateParams",
							(EventService: IEventService, $stateParams: ui.StateParams) =>
								EventService.getByEventId($stateParams["eventId"])
						],
						user_rsvp: [
							"event",
							"RsvpService",
							(event: IEvent, RsvpService: IRsvpService) =>
								RsvpService.getMyRsvpByEventId(event.event_id)
						],
						event_organization: ["event", (event: IEvent) => event.Organization()],
						organization_role: [
							"event_organization",
							(org: IOrganization) => org.Role()
						],
						event_comments: ["event", (event: IEvent) => event.Comments()],
						event_addresses: ["event", (event: IEvent) => event.Addresses()],
						event_skills: ["event", (event: IEvent) => event.Skills()],
						event_rsvps: [
							"event",
							"organization_role",
							"$q",
							(
								event: IEvent,
								organization_role: IPermission,
								$q: angular.IQService
							): angular.IPromise<IRsvp[]> =>
								organization_role && organization_role.write
									? event.Rsvps()
									: $q.when(null)
						],
						event_times: [
							"event",
							"organization_role",
							"$q",
							(
								event: IEvent,
								organization_role: IPermission,
								$q: angular.IQService
							): angular.IPromise<ITime[]> =>
								organization_role && organization_role.write
									? event.Times()
									: $q.when(null)
						]
					}
				})
				.state({
					name: "events.event.detail",
					url: "/detail",
					templateUrl: "/templates/events/event/detail.html",
					controller: "DetailController",
					controllerAs: "detailCtrl"
				});
		}
	]);
