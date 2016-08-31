<?php foreach($addresses as $address) : ?>
<?php echo $this->Html->tag('iframe', '', array(
	'src' => sprintf('https://www.google.com/maps/embed/v1/place?key=%s&q=%s',
		Configure::read('Google.maps.api_key'),
		$address['one_line']
	),
	'width' => '100%',
	'frameborder' => 0,
	'style' => $this->Html->style(array('border' => 'solid 1px #ddd'))
) ); ?>
<p><em><?php echo h( $address['one_line'] ); ?></em></p>
<?php endforeach; ?>