<?php if( !empty($addresses) ) :

	$map_sprint = '<iframe width="100%%" height="300" frameborder="0" style="border:0" src="https://www.google.com/maps/embed/v1/place?key=%1$s&amp;q=%2$s"></iframe>';

	if( !isset($title) )
	{
		$title = 'Addresses';
	}

?>
<div class="row">
	<div class="col-md-12">
		<h3><?php echo h( __($title) ); ?></h3>

<?php	foreach( $addresses as $address ) :

			switch($address['type'])
			{
				case 'physical':
					$say = __('Physical Address');
					break;
				case 'mailing':
					$say = __('Mailing Address');
					break;
				case 'both':
					$say = __('Physical/Mailing Address');
					break;
			}

?>
			<div class="row">
				<div class="col-md-4">
					<address>
						<?php echo $this->Html->tag('h4', $say); ?>
						<?php echo h($address['address1']); ?><br>
						<?php echo !empty($address['address2'])? h($address['address2']) . '<br>' : ''; ?>
						<?php echo sprintf('%s, %s %s', $address['city'], $address['state'], $address['zip']); ?>
					</address>
				</div>
				<div class="col-md-8">
					<?php
						if( $address['type'] != 'mailing' )
						{
							$address_sprint = sprintf(
								'%s, %s, %s',
								$address['address1'],
								$address['city'],
								$address['state']
							);

							echo sprintf(
								$map_sprint,
								urlencode( Configure::read('Google.maps.api_key') ),
								urlencode($address_sprint)
							);
						}
						else
						{
							echo '&nbsp;';
						}
					?>
				</div>
			</div>
		<?php endforeach; ?>

	</div>
</div>
<?php endif; ?>