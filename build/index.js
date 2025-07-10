/**
 * PostGrid Block Registration
 * 
 * This is a simplified version that works without build tools
 */
(function() {
	'use strict';

	var __ = wp.i18n.__;
	var registerBlockType = wp.blocks.registerBlockType;
	var createElement = wp.element.createElement;
	var Fragment = wp.element.Fragment;
	var InspectorControls = wp.blockEditor.InspectorControls;
	var PanelBody = wp.components.PanelBody;
	var RangeControl = wp.components.RangeControl;
	var SelectControl = wp.components.SelectControl;
	var ToggleControl = wp.components.ToggleControl;
	var ServerSideRender = wp.serverSideRender;
	var useBlockProps = wp.blockEditor.useBlockProps;

	// Register the PostGrid block
	registerBlockType('postgrid/postgrid', {
		title: __('PostGrid', 'postgrid'),
		description: __('Display posts in a responsive grid layout', 'postgrid'),
		icon: 'grid-view',
		category: 'widgets',
		attributes: {
			postsPerPage: {
				type: 'number',
				default: 6
			},
			orderBy: {
				type: 'string',
				default: 'date'
			},
			order: {
				type: 'string',
				default: 'desc'
			},
			selectedCategory: {
				type: 'number',
				default: 0
			},
			columns: {
				type: 'number',
				default: 3
			},
			showDate: {
				type: 'boolean',
				default: true
			},
			showExcerpt: {
				type: 'boolean',
				default: true
			}
		},
		supports: {
			html: false,
			align: ['wide', 'full'],
			spacing: {
				margin: true,
				padding: true
			}
		},
		edit: function(props) {
			var attributes = props.attributes;
			var setAttributes = props.setAttributes;

			var blockProps = useBlockProps();

			return createElement(
				Fragment,
				null,
				createElement(
					InspectorControls,
					null,
					createElement(
						PanelBody,
						{ title: __('PostGrid Settings', 'postgrid') },
						createElement(RangeControl, {
							label: __('Number of posts', 'postgrid'),
							value: attributes.postsPerPage,
							onChange: function(value) {
								setAttributes({ postsPerPage: value });
							},
							min: 1,
							max: 20
						}),
						createElement(SelectControl, {
							label: __('Order by', 'postgrid'),
							value: attributes.orderBy,
							options: [
								{ label: __('Date', 'postgrid'), value: 'date' },
								{ label: __('Title', 'postgrid'), value: 'title' },
								{ label: __('Menu order', 'postgrid'), value: 'menu_order' }
							],
							onChange: function(value) {
								setAttributes({ orderBy: value });
							}
						}),
						createElement(SelectControl, {
							label: __('Order', 'postgrid'),
							value: attributes.order,
							options: [
								{ label: __('Descending', 'postgrid'), value: 'desc' },
								{ label: __('Ascending', 'postgrid'), value: 'asc' }
							],
							onChange: function(value) {
								setAttributes({ order: value });
							}
						}),
						createElement(RangeControl, {
							label: __('Columns', 'postgrid'),
							value: attributes.columns,
							onChange: function(value) {
								setAttributes({ columns: value });
							},
							min: 1,
							max: 6
						}),
						createElement(ToggleControl, {
							label: __('Show date', 'postgrid'),
							checked: attributes.showDate,
							onChange: function(value) {
								setAttributes({ showDate: value });
							}
						}),
						createElement(ToggleControl, {
							label: __('Show excerpt', 'postgrid'),
							checked: attributes.showExcerpt,
							onChange: function(value) {
								setAttributes({ showExcerpt: value });
							}
						})
					)
				),
				createElement(
					'div',
					blockProps,
					createElement(ServerSideRender, {
						block: 'postgrid/postgrid',
						attributes: attributes
					})
				)
			);
		},
		save: function() {
			// Dynamic block - rendered server-side
			return null;
		}
	});

	// Also register the caxton/posts-grid block
	registerBlockType('caxton/posts-grid', {
		title: __('Posts Grid (Legacy)', 'postgrid'),
		description: __('Legacy Caxton posts grid - redirects to PostGrid', 'postgrid'),
		icon: 'grid-view',
		category: 'widgets',
		attributes: {
			postsPerPage: {
				type: 'number',
				default: 6
			},
			orderBy: {
				type: 'string',
				default: 'date'
			},
			order: {
				type: 'string',
				default: 'desc'
			},
			selectedCategory: {
				type: 'number',
				default: 0
			},
			columns: {
				type: 'number',
				default: 3
			},
			showDate: {
				type: 'boolean',
				default: true
			},
			showExcerpt: {
				type: 'boolean',
				default: true
			}
		},
		supports: {
			html: false,
			align: ['wide', 'full']
		},
		edit: function(props) {
			var attributes = props.attributes;
			var blockProps = useBlockProps();

			return createElement(
				'div',
				blockProps,
				createElement(ServerSideRender, {
					block: 'caxton/posts-grid',
					attributes: attributes
				})
			);
		},
		save: function() {
			return null;
		}
	});

})();