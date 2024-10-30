(function () {
	if( typeof crazyPills === 'undefined' )
		return;
	var config = crazyPills.config;

	jQuery( Object.keys( config ) ).each( function( i, plugin ) {
		tinymce.PluginManager.add( plugin, function( editor, url ) {

			function mapColors( type ) {
				return config[ type ].def;
			}

			function renderColorPicker() {
				var self = this, colors, color, html, last, x, y, i, id = self._id, count = 0, type;

				type = self.settings.origin;

				function getColorCellHtml( color, title, value ) {
					return (
						'<td class="mce-grid-cell">' +
							'<div id="' + id + '-' + (count++) + '"' +
								' data-value="' + value + '"' +
								' role="option"' +
								' tabIndex="-1"' +
								' style="' + (color ? 'background-color: ' + color : '') + '"' +
								' title="' + title + '">' +
							'</div>' +
						'</td>'
					);
				}

				colors = mapColors(type);

				html = '<table class="mce-grid mce-grid-border mce-colorbutton-grid" role="list" cellspacing="0"><tbody>';
				last = colors.length - 1;

				for (y = 0; y < config[ type ].grid[0]; y++) {
					html += '<tr>';

					for (x = 0; x < config[ type ].grid[1]; x++) {
						i = y * config[ type ].grid[1] + x;

						if (i > last) {
							html += '<td></td>';
						} else {
							color = colors[i];
							html += getColorCellHtml( color.color, color.text, color.value );
						}
					}

					html += '</tr>';
				}

				html += '</tbody></table>';

				return html;
			}

			function onPanelClick( e ) {
				var type = this.settings.origin;
				var template = config[ type ].template;
				var value = e.target.getAttribute( 'data-value' );
				var text = editor.selection.getContent( { format: 'text' } ) || config[ type ].default_text;

				template = template.replace( '%text%', text ).replace( '%value%', value );
				editor.insertContent( template );
			}

			function onButtonClick( e ) {
				jQuery( e.target ).closest( '.mce-widget' ).find( '.mce-open' ).click();
			}

			editor.addButton( plugin, {
				type: 'colorbutton',
				tooltip: config[ plugin ].label,
				format: 'forecolor',
				panel: {
					origin: plugin, /* this key is used later in onPanelClick to identify which button has been clicked */
					role: 'application',
					ariaRemember: true,
					html: renderColorPicker,
					onclick: onPanelClick
				},
				onclick: onButtonClick
			});

		} );
	} );
})();