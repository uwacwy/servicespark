import * as angular from "angular";
import {IAddress, ICommentParent, IEvent, IOrganization, IPermission, IRsvp, ISkill, ITime} from "../ServiceSpark";

export class DetailController {
    public hello: string = "World";

    public static $inject: string[] = [
        '$scope',
        'event',
        'user_rsvp',
        'event_organization',
        'event_rsvps',
        'event_addresses',
        'event_comments',
        'event_times',
        'event_skills',
        'organization_role'
    ];
    constructor(
        public $scope: angular.IScope,
        public event: IEvent,
        public user_rsvp: IRsvp,
        public event_organization: IOrganization,
        public event_rsvps: IRsvp[],
        public event_addresses: IAddress[],
        public event_comments: ICommentParent[],
        public event_times: ITime[],
        public event_skills: ISkill[],
        public organization_role: IPermission
    ) {
        $scope.$on('rsvp_updated', function(evt: angular.IAngularEvent) {
            console.log('rsvp_updated', arguments);
        })
        console.log("DetailController init");
    }

    public appendUsername(username: string): void {
        this.$scope.$root.$broadcast('append_username', username);
    }

    public PrettyRsvpStatus(status: "going" | "maybe" | "not_going"): string {
        if( status === "going" )
            return "Going";

        if( status == "maybe" )
            return "Interested";

        if( status === "not_going" ) {
            return "Not Going";
        }
    }

    public getSkills() {
        return this.event_skills.map( skill => skill.skill ).join(', ');
    }
}
