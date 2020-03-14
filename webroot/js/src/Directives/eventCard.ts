import { IEvent } from "@models/Event";

export const eventCardFactory = function(): angular.IDirective {
	interface IEventCardScope extends angular.IScope {
		event: IEvent;
		getOrElse<T>(collection: T[], otherwise: T): T;
	}
	return {
		scope: {
			event: "="
		},
		replace: true,
		templateUrl: "/templates/events/event/card.html",
		link: function(
			scope: IEventCardScope,
			element: angular.IAugmentedJQuery,
			attrs: angular.IAttributes,
			ngModel: angular.INgModelController
		) {
			element.addClass("event actionable");
			scope.getOrElse = function<T>(collection: T[], otherwise: T): T {
				if (collection[0]) {
					return collection[0];
				} else {
					return otherwise;
				}
			};
		}
	};
};
