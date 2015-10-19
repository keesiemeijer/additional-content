<div class="ac-repeat-container">
	<p class="ac-option-content">
		<label class="ac-content" for="ac_additional_content-<?php echo $i; ?>"><?php echo $label; ?>:</label>
		<textarea id="ac_additional_content-<?php echo $i; ?>" class="large-text" name="ac_additional_content[<?php echo $i; ?>][additional_content]" cols="40" rows="2" <?php echo $describedby; ?>><?php echo esc_textarea( $fields['additional_content'] ); ?></textarea>
	</p>
	<div id="ac-options-<?php echo $i; ?>" class="ac-options<?php echo $visible; ?>"> 
		<p class="<?php echo sanitize_html_class( $class['append_prepend'] ); ?>">
			<label class="selectit" for="ac_prepend-<?php echo $i; ?>" style="margin-right:2em;">
				<input id="ac_prepend-<?php echo $i; ?>" type="checkbox" name="ac_additional_content[<?php echo $i; ?>][prepend]" value="on" data-ac-type="prepend" <?php checked( $fields['prepend'], 'on' );  ?>/>
				<?php echo $text['prepend']; ?>
			</label>
			<label class="selectit" for="ac_append-<?php echo $i; ?>">
				<input id="ac_append-<?php echo $i; ?>" type="checkbox" name="ac_additional_content[<?php echo $i; ?>][append]"  value="on" data-ac-type="append" <?php checked( $fields['append'], 'on' ); ?>/>
				<?php echo $text['append']; ?>
			</label>
		</p>
		<p class="<?php echo sanitize_html_class( $class['priority'] );  ?>">
			<label for="ac_priority-<?php echo $i; ?>"><?php echo $text['priority']; ?></label>
			<input id="ac_priority-<?php echo $i; ?>" class="small-text widefat" type="number" step="1" min="0" name="ac_additional_content[<?php echo $i; ?>][priority]" size="3" value="<?php esc_attr_e( $fields['priority'] ); ?>" />
			<br/>
			<?php if( !empty( $text['priority_info'] ) ) : ?>
			<span class="description">
				<?php echo $text['priority_info'] ?>
			</span>
			<?php endif; ?>
		</p>
	</div>
	<div id="ac-actions-<?php echo $i; ?>" class="ac-actions<?php echo $visible; ?>">
		<input id="ac_remove-<?php echo $i; ?>" class="button ac-remove" type="submit" value="<?php esc_attr_e( $text['remove_row'] ); ?>" name="ac_additional_content[<?php echo $i; ?>][remove]">
	</div>
</div>