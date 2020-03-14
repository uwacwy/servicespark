import * as moment from "moment";
import { ICommentParent } from "../Model/Comment";

export const commentDisplayDirectiveFactory = function(): angular.IDirective {
	interface ICommentDisplayScope extends angular.IScope {
		comment: ICommentParent;
		eventId: any;
		appendUsername(username: string): void;
	}

	return {
		templateUrl: "/templates/comments/comment/display.html",
		replace: true,
		scope: {
			comment: "=",
			eventId: "="
		},
		link: (
			scope: ICommentDisplayScope,
			instanceElement,
			instanceAttributes,
			controller,
			transclude
		) => {
			console.log("comment-display", scope);

			scope.appendUsername = function(username: string) {
				scope.$root.$broadcast("append_username", username);
			};


		}
	};
};
