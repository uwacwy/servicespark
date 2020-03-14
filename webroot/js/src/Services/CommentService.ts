import {
	ICommentParent,
	ICommentStub,
	ICommentsParentContainer,
	ICommentParentContainer
} from "../Model/Comment";
import { url, defaultConfig } from "../Helpers/ApiHelper";

export interface ICommentService {
	getThreadByEventId(event_id: string): angular.IPromise<ICommentParent[]>;
	save(comment: ICommentStub): angular.IPromise<ICommentParent>;
}

export const CommentServiceFactory = function(
	$http: angular.IHttpService,
	$q: angular.IQService
): ICommentService {
	return {
		getThreadByEventId: function(
			event_id: string
		): angular.IPromise<ICommentParent[]> {
			return $http
				.get<ICommentsParentContainer>(
					url("events", event_id, "comments"),
					defaultConfig
				)
				.then(success => success.data.comments);
		},
		save(comment: ICommentStub): angular.IPromise<ICommentParent> {

			if (comment.body.trim() === "") {
				return $q.reject("Comment body cannot be empty");
			}
			
			return $http
				.post<ICommentParentContainer>(
					url("events", comment.event_id, "comments"),
					comment,
					defaultConfig
				)
				.then(success => success.data.comment);
		}
	};
};
