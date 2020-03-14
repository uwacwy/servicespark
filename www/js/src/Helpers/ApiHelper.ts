export function url(...chunks: string[]): string {
	return "/api/" + chunks.join("/");
};

export const defaultConfig = {
    headers: {
        Accept: "application/json"
    }
};