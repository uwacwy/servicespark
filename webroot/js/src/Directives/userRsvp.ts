import { IRsvp } from "../Model/Rsvp";
import * as angular from "angular";

export const userRsvpDirectiveFactory = function() {
    interface IUserRsvpDirectiveScope extends angular.IScope {
        userRsvp: IRsvp;
        eventId: any;
        onChange: () => void;
    }

    return {
        templateUrl: "/templates/directives/radio-bar__rsvp.html",
        scope: {
            userRsvp: "=",
            eventId: "="
        },
        link: function(
            scope: IUserRsvpDirectiveScope,
            element: angular.IAugmentedJQuery,
            attrs: angular.IAttributes,
            ngModel: angular.INgModelController
        ) {
            scope.onChange = () => {
                let merged = angular.extend(
                    {
                        event_id: scope.eventId
                    },
                    scope.userRsvp
                );
                console.log("rsvp changed", merged);
                scope.$emit("user_rsvp_updated", merged);
            };
        }
    };
}