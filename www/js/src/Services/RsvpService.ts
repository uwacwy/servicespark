import { IRsvp, IRsvpContainer, IRsvpsContainer } from "../Model/Rsvp";
import { url } from "../Helpers/ApiHelper";

export interface IRsvpService {
	getMyRsvpByEventId(event_id: string): angular.IPromise<IRsvp>;
	getAllByEventId(event_id: string): angular.IPromise<IRsvp[]>;
	updateRsvp(rsvp: IRsvp): angular.IPromise<IRsvp>;
}

export const RsvpServiceFactory = function($http: angular.IHttpService, $q: angular.IQService): IRsvpService {
    let config = {
        headers: {
            Accept: "application/json"
        }
    };

    return {
        getMyRsvpByEventId: function(event_id: string) {
            return $http
                .get<IRsvpContainer>(
                    url("events", event_id, "rsvps", "me"),
                    config
                )
                .then(success => {
                    let deferred = $q.defer<IRsvp>();
                    if (success.data) {
                        deferred.resolve(success.data.rsvp);
                    } else {
                        deferred.resolve({
                            event_id: event_id
                        });
                    }
                    return deferred.promise;
                });
        },
        getAllByEventId: function(event_id: string) {
            return $http
                .get<IRsvpsContainer>(
                    url("events", event_id, "rsvps"),
                    config
                )
                .then(success => success.data.rsvps);
        },
        updateRsvp: function(rsvp: IRsvp) {
            return $http
                .patch<IRsvpContainer>(
                    url("events", rsvp.event_id, "rsvps", "me"),
                    {
                        rsvp: rsvp
                    },
                    config
                )
                .then(success => success.data.rsvp);
        }
    };
}