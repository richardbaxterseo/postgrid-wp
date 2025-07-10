import { __ } from '@wordpress/i18n';
import { Component } from '@wordpress/element';
import { 
	InspectorControls,
	BlockControls,
	AlignmentToolbar 
} from '@wordpress/block-editor';
import {
	PanelBody,
	RangeControl,
	SelectControl,
	ToggleControl,
	Placeholder,
	Spinner,
} from '@wordpress/components';
import { withSelect } from '@wordpress/data';
import apiFetch from '@wordpress/api-fetch';
import ServerSideRender from '@wordpress/server-side-render';
import CategorySelect from './category-select';

class Edit extends Component {
	render() {
		const {
			attributes,
			setAttributes,
			categories,
			className,
		} = this.props;

		const {
			postsPerPage,
			columns,
			orderBy,
			order,
			displayFeaturedImage,
			displayTitle,
			displayExcerpt,
			displayDate,
			displayAuthor,
		} = attributes;

		const inspectorControls = (
			<InspectorControls>
				<PanelBody title={ __( 'Query Settings', 'simple-posts-grid' ) }>
					<CategorySelect
						categories={ categories }
						selectedCategories={ attributes.categories }
						onChange={ ( categories ) => setAttributes( { categories } ) }
					/>
					
					<RangeControl
						label={ __( 'Number of posts', 'simple-posts-grid' ) }
						value={ postsPerPage }
						onChange={ ( value ) => setAttributes( { postsPerPage: value } ) }
						min={ 1 }
						max={ 20 }
					/>
					
					<SelectControl
						label={ __( 'Order by', 'simple-posts-grid' ) }
						value={ orderBy }
						options={ [
							{ label: __( 'Date', 'simple-posts-grid' ), value: 'date' },
							{ label: __( 'Title', 'simple-posts-grid' ), value: 'title' },
							{ label: __( 'Menu order', 'simple-posts-grid' ), value: 'menu_order' },
							{ label: __( 'Random', 'simple-posts-grid' ), value: 'rand' },
						] }
						onChange={ ( value ) => setAttributes( { orderBy: value } ) }
					/>
					
					<SelectControl
						label={ __( 'Order', 'simple-posts-grid' ) }
						value={ order }
						options={ [
							{ label: __( 'Descending', 'simple-posts-grid' ), value: 'desc' },
							{ label: __( 'Ascending', 'simple-posts-grid' ), value: 'asc' },
						] }
						onChange={ ( value ) => setAttributes( { order: value } ) }
					/>
				</PanelBody>

				<PanelBody title={ __( 'Layout Settings', 'simple-posts-grid' ) }>
					<RangeControl
						label={ __( 'Columns', 'simple-posts-grid' ) }
						value={ columns }
						onChange={ ( value ) => setAttributes( { columns: value } ) }
						min={ 1 }
						max={ 6 }
					/>
				</PanelBody>
				
				<PanelBody title={ __( 'Display Settings', 'simple-posts-grid' ) }>
					<ToggleControl
						label={ __( 'Display featured image', 'simple-posts-grid' ) }
						checked={ displayFeaturedImage }
						onChange={ ( value ) => setAttributes( { displayFeaturedImage: value } ) }
					/>
					
					<ToggleControl
						label={ __( 'Display title', 'simple-posts-grid' ) }
						checked={ displayTitle }
						onChange={ ( value ) => setAttributes( { displayTitle: value } ) }
					/>
					
					<ToggleControl
						label={ __( 'Display excerpt', 'simple-posts-grid' ) }
						checked={ displayExcerpt }
						onChange={ ( value ) => setAttributes( { displayExcerpt: value } ) }
					/>
					
					<ToggleControl
						label={ __( 'Display date', 'simple-posts-grid' ) }
						checked={ displayDate }
						onChange={ ( value ) => setAttributes( { displayDate: value } ) }
					/>
					
					<ToggleControl
						label={ __( 'Display author', 'simple-posts-grid' ) }
						checked={ displayAuthor }
						onChange={ ( value ) => setAttributes( { displayAuthor: value } ) }
					/>
				</PanelBody>
			</InspectorControls>
		);

		return (
			<>
				{ inspectorControls }
				<div className={ className }>
					<ServerSideRender
						block="simple-posts-grid/posts-grid"
						attributes={ attributes }
					/>
				</div>
			</>
		);
	}
}

export default withSelect( ( select ) => {
	const { getEntityRecords } = select( 'core' );
	
	return {
		categories: getEntityRecords( 'taxonomy', 'category', { per_page: -1 } ),
	};
} )( Edit );
