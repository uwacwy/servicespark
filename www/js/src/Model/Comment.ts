import { IUser } from "./User";

export interface ICommentStub {
	body: string;
	parent_id: string;
	event_id: string;
}

export interface IComment extends ICommentStub {
	comment_id?: string;
	user_id?: string;

	created?: string | Date;
	modified?: string | Date;

	User?: IUser;
}

export interface ICommentContainer {
	comment: IComment;
}

export interface ICommentParent {
	Comment: IComment;
	User: IUser;
	children: ICommentParent[];
}

export interface ICommentParentContainer {
	comment: ICommentParent;
}

export interface ICommentsParentContainer {
	comments: ICommentParent[];
}
