import { IHasManyEvent } from "@models/Event";
import { IHasOneSkill } from "@models/Skill";

export class RecommendedController {
	public static $inject: string[] = ["recommended"];

	constructor(public recommended: Array<IHasManyEvent | IHasOneSkill>) {
		console.log("Recommended Events", recommended);
	}
}
