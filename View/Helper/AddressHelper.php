<?php
/*
	AddressHelper
	--
	generates display controls to make dealing with addresses in forms easy
*/

App::uses('AppHelper', 'View/Helper');

class AddressHelper extends AppHelper
{
	public $helpers = array('Html', 'Js', 'Form');

	public function addBlock()
	{

		$rtn = "";

		$addressType = array('physical', 'mailing', 'both');

		$i = 0;
		foreach($addressType as $type)
		{
			$rtn .= sprintf('<div class="address %s">', $type);
			switch ($type)
			{
				case 'physical':
					$rtn .= '<h3>Physical Address</h3>';
					break;
				case 'mailing':
					$rtn .= '<h3>Mailing Address</h3>';
					break;
				case 'both':
					$rtn .= '<h3>Physical and Mailing Address</h3>';
					break;
			}

			$rtn .= $this->Form->input("Address.$i.type", array('type' => "hidden", 'value' => $type));
			foreach( array('address1', 'address2', 'city', 'state', 'zip') as $field)
			{
				$rtn .= $this->Form->input("Address.$i.$field");
			}

			$rtn .= '</div>';

			$i++;
		}

		return $rtn;
	}

	public function editBlock($addresses)
	{

		$rtn = "";

		$i = 0;

		foreach($addresses as $address)
		{
			$rtn .= sprintf('<div class="address %s" id="address-%u">', $address['type'], $address['address_id']);

			switch ($address['type'])
			{
				case 'physical':
					$rtn .= '<h3>Physical Address</h3>';
					break;
				case 'mailing':
					$rtn .= '<h3>Mailing Address</h3>';
					break;
				case 'both':
					$rtn .= '<h3>Physical and Mailing Address</h3>';
					break;
			}

			$rtn .= $this->Form->input("Address.$i.address_id");

			foreach( array('address1', 'address2', 'city', 'state', 'zip') as $field)
			{
				$rtn .= $this->Form->input("Address.$i.$field");
			}

			$rtn .= '</div>';

			$i++;
		}

		return $rtn;
	}

	public function newAddress()
	{
		return sprintf('<div class="address">%s</div>');
	}

	public function editAddress($address)
	{

	}

	public function printAddress($address)
	{

	}
}