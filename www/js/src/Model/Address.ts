export type AddressType = "both" | "mailing" | "physical";

export interface IAddress {
	address_id?: number;
	address1?: string;
	address2?: string;
	city?: string;
	state?: string;
	zip?: string;
	type?: AddressType;
}

export interface IAddressContainer {
	addresses: IAddress[];
}

