<span class="autocomplete-wrapper">
	<a class="cancel" href="#">&times;</a>
	SKILL NAME HERE
	<?php echo sprintf('<input type="hidden" name="data[Skill][Skill][]" value="%u" />',
		$this->request->data['Skill']['Skill'][$i]); ?>
</span>