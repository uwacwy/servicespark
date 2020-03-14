import { ICommentService } from "../Services/CommentService";
import { ICommentParent, ICommentStub } from "../Model/Comment";

export const commentFormDirectiveFactory = function(
	$timeout: angular.ITimeoutService,
	CommentService: ICommentService
): angular.IDirective {
	interface ICommentFormScope extends angular.IScope {
		parentId: string;
		eventId: string;
		parentChildren: ICommentParent[];
		isCommenting: boolean;
		type: "static" | "reply";
		comment: ICommentStub;

		appendUsername(username: string): void;
		isReply(): boolean;
		isStatic(): boolean;
		startReply(): void;
		cancelReply(): void;
		submitReply(): void;
	}

	return {
		templateUrl: "/templates/comments/comment/form.html",
		scope: {
			parentId: "=",
			eventId: "=",
			parentChildren: "=",
			type: "@",
			isCommenting: "@"
		},
		link: (
			scope: ICommentFormScope,
			instanceElement: any,
			instanceAttributes,
			controller,
			transclude
		) => {
			console.log("comment-form", scope);

			scope.comment = {
				body: "",
				event_id: scope.eventId,
				parent_id: scope.parentId || null
			};

			scope.isReply = () => scope.type === "reply";
			scope.isStatic = () => scope.type === "static";

			scope.$on("append_username", (event2, username) => {
				if (scope.isStatic() || scope.isCommenting) {
					scope.comment.body = (scope.comment.body + " @" + username).trim() + " ";
					$timeout(() => {
						instanceElement.find("textarea").focus();
					}, 0);
				}
			});

			scope.startReply = () => {
				scope.isCommenting = true;
				$timeout(() => {
					instanceElement.find("textarea").focus();
				}, 0);
			};

			scope.cancelReply = () => {
				scope.comment.body = "";
				scope.isCommenting = false;
			};

			scope.submitReply = () => {
				CommentService.save(scope.comment).then(comment => {
					comment.Comment.modified = new Date();
					comment.Comment.created = new Date();
					scope.parentChildren.push(comment);
					scope.cancelReply();
				});
				console.log("comment-form", "submitReply clicked");
			};
		}
	};
};
