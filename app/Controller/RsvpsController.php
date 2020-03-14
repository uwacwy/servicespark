<?php
/**
 * Created by PhpStorm.
 * User: bkovach
 * Date: 3/11/18
 * Time: 9:20 PM
 */

class RsvpsController extends AppController
{
	public function api_patch_rsvp($rsvp_id) {
		$rsvp = $this->Rsvp->find('first', array(
			'conditions' => array(
				'Rsvp.rsvp_id' => $rsvp_id,
				'Rsvp.user_id' => $this->Auth->user('user_id')
			),
			'contain' => array()
		));

		$incoming = (array)$this->request->input('json_decode')->rsvp;

		if ($rsvp) { // Updating
			unset($rsvp['Rsvp']['created'], $rsvp['Rsvp']['modified']);
		} else { // Inserting
			throw new NotFoundException("Unable to update RSVP");
		}

		$rsvp = array_merge($rsvp['Rsvp'], $incoming);

		if ($this->Rsvp->save($rsvp)) {
			$updated = $this->Rsvp->findByRsvpId($rsvp['rsvp_id'], array(
				'contain' => array()
			));
			$this->set('rsvp', $updated['Rsvp']);
			$this->set('_serialize', array('rsvp'));
		} else {
			throw new InternalErrorException(__('Unable to save RSVP'));
		}
	}
}