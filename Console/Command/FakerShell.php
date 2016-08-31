<?php

App::uses('CakeEmail', 'Network/Email');
App::uses('CakeTime', 'Utility');

require APP . "Vendor/autoload.php";

class FakerShell extends AppShell
{
	public $uses = array('User', 'Organization', 'Event', 'Skill');
	
	var $faker;

	public function main()
	{
		$this->faker = \Faker\Factory::create();
		
		// create skills and addresses
		//  $this->users(25);
		//  $this->organizations(25);
		//  $this->events(25);
		
		$this->skills(100);
		//  $this->addresses(); // one fore each user/organization/event
		
		// $this->permissions();
		
		// $this->rsvps();
		
		//$this->event_comments();
	}
	
	public function organizations($O)
	{
		for($o = 0; $o < $O; $o++)
		{
			$organizations[$o]['Organization'] = array(
				'name' => $this->faker->company,
				'description' => $this->faker->catchPhrase
			);
		}
		
		if(	$this->Organization->saveAll($organizations, array('deep' => true) ) )
		{
		 	$this->out('created '.$O.' organizations');
		}
	}
	
	public function events($count)
	{
		$organization_ids = array_keys( $this->Organization->find('list') );
		for($i = 0; $i < $count; $i++)
		{
			$event[] = array(
				'organization_id' => $this->faker->randomElement($organization_ids),
				'title' => $this->faker->sentence(6),
				'description' => $this->faker->text(900),
				'start_time' => $this->faker->dateTimeBetween('-2 hour', '-1 hour')->format("Y-m-d H:i:s"),
				'stop_time' => $this->faker->dateTimeBetween('+1 hour', '+2 hour')->format("Y-m-d H:i:s"),
				'rsvp_desired' => $this->faker->numberBetween(1, 100),
				'rsvp_count' => 0,
				'comment_count' => 0,
				'missed_punches' => 0,
				'start_token' => $this->faker->bothify('#?#?#?#'),
				'stop_token' => $this->faker->bothify('?#?#?#?')
			);
		}
		
		if( $this->Event->saveAll($event) )
			$this->out("Created $count events");
	}
	
	public function users($count)
	{
		for($i = 0; $i < $count; $i++)
		{
			$fn = $this->faker->firstName;
			$ln = $this->faker->lastName;
			$username = $this->faker->username;
			
			$users[$i]['User'] = array(
				'first_name' => $fn,
				'last_name' => $ln,
				'password' => $username,
				'username' => $this->faker->username,
				'email' => "bradkovach+" . $username . "@gmail.com"
			);

		}
		
		if( $this->User->saveAll($users) )
		{
			$this->out("Created $count users");
		}
		else
		{
			$this->out('problem creating users');
			
		}
	}
	
	public function skills($count)
	{
		// $event_ids = array_keys($this->User->find('list'));
		// $user_ids = array_keys($this->User->find('list'));
		
		for($s = 0; $s < $count; $s++)
		{
			$skill[$s]['Skill'] = array(
				'skill' => $this->faker->bs
			);
			// $skill[$s]['User'] = $this->faker->randomElements($user_ids, 5);
			// $skill[$s]['Event'] = $this->faker->randomElements($event_ids, 5);
		}
		
		if( $this->Skill->saveAll($skill) )
			$this->out("Created $count skills");
	}
	
	public function addresses()
	{
		// $a % 3 == 0 => event
		// $a % 3 == 1 => organization
		// $a % 3 == 2  => user
		
		$event_ids = array_keys( $this->Event->find('list') );
		$organization_ids = array_keys( $this->Organization->find('list') );
		$user_ids = array_keys( $this->User->find('list') );
		
		$count = count($event_ids) + count($organization_ids) + count($user_ids);
		
		for($a = 0; $a < $count; $a++)
		{
			$address[$a]['Address'] = array(
				'address1' => $this->faker->streetAddress,
				'address2' => $this->faker->secondaryAddress,
				'city' => $this->faker->city,
				'state' => $this->faker->stateAbbr,
				'zip' => $this->faker->postCode,
				'type' => $this->faker->randomElement( array('both', 'mailing', 'physical') )
			);
			
			if( $a % 3 == 0 )
				$address[$a]['Event'] = array( array_pop($event_ids) );
			if( $a % 3 == 1 )
				$address[$a]['Organization'] = array( array_pop($organization_ids) );
			if( $a % 3 == 2 )
				$address[$a]['User'] = array( array_pop($user_ids) );
				
		}
		
		if( $this->User->Address->saveAll($address) )
			$this->out("Created $count addresses");
	}
	
	public function rsvps()
	{
		$event_ids = array_keys( $this->Event->find('list') );
		$user_ids = array_keys( $this->User->find('list') );
		$total = count($event_ids) * count($user_ids);
		
		foreach($event_ids as $event_id)
		{
			foreach($user_ids as $user_id)
			{
				$rsvp[] = array(
					'event_id' => $event_id,
					'user_id' => $user_id,
					'status' => $this->faker->randomElement( array('going', 'not_going', 'maybe') )
				);
			}
		}
		if( $this->User->Rsvp->saveAll($rsvp) )
			$this->out("Created $total RSVPs");
	}
	
	public function event_comments()
	{
		$user_ids = array_keys( $this->User->find('list') );
		$event_ids = array_keys( $this->Event->find('list') );
		
		$i = 0;
		foreach( $user_ids as $user_id )
		{
			foreach( $event_ids as $event_id )
			{
				$comment[] = array(
					'event_id' => $event_id,
					'user_id' => $user_id,
					'parent_id' => null,
					'body' => $this->faker->text(200)
				);
			}
		}
		
		if( $this->User->Comment->saveAll($comment) )
			$this->out("Created " . $this->User->Comment->getAffectedRows() . " comments");
	}
	
	public function permissions()
	{
		$users = $this->User->find('list');
		$organizations = $this->Organization->find('list');
		
		foreach($organizations as $organization_id => $name)
		{
			foreach($users as $user_id => $username)
			{
				$permission[] = array(
					'user_id' => $user_id,
					'organization_id' => $organization_id,
					'following' => $this->faker->randomElement( array(0,1) ),
					'publish' => $this->faker->randomElement( array(0,1) ),
					'read' => $this->faker->randomElement( array(0,1) ),
					'write' => $this->faker->randomElement( array(0,1) ),
				);
			}
		}
		
		if( $this->User->Permission->saveAll($permission) )
		{
			$this->out('valid permissions saved');
		}
	}
	
	public function event_time()
	{
		
	}
	
}