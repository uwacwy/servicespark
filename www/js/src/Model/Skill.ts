export interface ISkill {
	// Primary Key
	skill_id?: string;
	skill?: string;

	// Timestamps
	created?: string;
	modified?: string;
}

export interface IHasOneSkill {
	Skill: ISkill;
}

export interface ISkillContainer {
	skills: ISkill[];
}