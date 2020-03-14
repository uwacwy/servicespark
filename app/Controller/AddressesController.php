<?php
App::uses('AppController', 'Controller');
/**
 * Addresses Controller
 *
 * @property Address $Address
 * @property PaginatorComponent $Paginator
 */
class AddressesController extends AppController {

    public function api_organization($organization_id = null) {
        if( $organization_id == null ) {
            $organization_ids = $this->_GetUserOrganizationsByPermission('write');
        } else {
            $organization_ids = [$organization_id];
        }

        $organizations = $this->Address->Organization->find('all', [
            'conditions' => [
                'Organization.organization_id' => $organization_ids
            ],
            'contain' => [
                'Event' => ['Address']
            ]
        ]);

        $addresses = Hash::extract($organizations, '{n}.Event.{n}.Address.{n}');

        $this->set( [
            '_serialize' => ['addresses', 'organizations'],
            'organizations' => $organizations,
            'addresses' => $addresses
        ]);
    }
    
    public function event($event_id) {

    }

    public function user($user_id = null) {

    }

}
