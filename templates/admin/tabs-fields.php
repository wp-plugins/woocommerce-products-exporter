<h3><?php _e( 'Field Editor', 'woo_ce' ); ?></h3>
<p><?php _e( 'Customise the field labels for this export type by filling in the fields, an empty field label will revert to the default Store Exporter field label at export time.', 'woo_ce' ); ?></p>
<?php if( $fields ) { ?>
<form method="post" id="postform">
	<table class="form-table">
		<tbody>

			<tr>
				<th><?php _e( 'Field slug', 'woo_ce' ); ?></th>
				<th><?php _e( 'Label', 'woo_ce' ); ?></th>
				<th><?php _e( 'Hidden', 'woo_ce' ); ?></th>
			</tr>

	<?php foreach( $fields as $field ) { ?>
			<tr>
				<th scope="row"><label for="<?php echo $field['name']; ?>"><?php echo $field['name']; ?></label></th>
				<td class="align-left">
					<input type="text" name="fields[<?php echo $field['name']; ?>]" title="<?php echo $field['name']; ?>" placeholder="<?php echo $field['label']; ?>" value="<?php if( isset( $labels[$field['name']] ) ) { echo $labels[$field['name']]; } ?>" class="regular-text all-options" />
				</td>
				<td><input type="checkbox" name="hidden[<?php echo $field['name']; ?>] value="1"<?php checked( ( isset( $field['hidden'] ) ? $field['hidden'] : 0 ), 1 ); ?> /></td>
			</tr>

	<?php } ?>
		</tbody>
	</table>
	<!-- .form-table -->

	<p class="submit">
		<input type="submit" value="<?php _e( 'Save Changes', 'woo_ce' ); ?> " class="button-primary" />
	</p>
	<input type="hidden" name="action" value="save-fields" />
	<input type="hidden" name="type" value="<?php echo esc_attr( $export_type ); ?>" />

</form>
<?php } ?>