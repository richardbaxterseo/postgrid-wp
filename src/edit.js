/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { 
	PanelBody, 
	RangeControl, 
	SelectControl,
	ToggleControl,
	Spinner,
	Placeholder
} from '@wordpress/components';
import { useSelect } from '@wordpress/data';
import { useEffect, useState } from '@wordpress/element';
import apiFetch from '@wordpress/api-fetch';

/**
 * Editor component
 */
export default function Edit( { attributes, setAttributes } ) {
	const { 
		postsPerPage, 
		orderBy, 
		order, 
		selectedCategory, 
		columns,
		showDate,
		showExcerpt 
	} = attributes;
	
	const [ posts, setPosts ] = useState( [] );
	const [ isLoading, setIsLoading ] = useState( false );
	
	// Fetch categories
	const categories = useSelect( ( select ) => {
		const { getEntityRecords } = select( 'core' );
		const cats = getEntityRecords( 'taxonomy', 'category', { per_page: -1 } );
		return cats || [];
	}, [] );
	
	// Fetch posts when attributes change
	useEffect( () => {
		setIsLoading( true );
		
		const params = new URLSearchParams( {
			per_page: postsPerPage,
			orderby: orderBy,
			order: order,
			category: selectedCategory,
		} );
		
		apiFetch( {
			path: `/posts-grid/v1/posts?${params}`,
		} )
			.then( ( fetchedPosts ) => {
				setPosts( fetchedPosts );
				setIsLoading( false );
			} )
			.catch( () => {
				setPosts( [] );
				setIsLoading( false );
			} );
	}, [ postsPerPage, orderBy, order, selectedCategory ] );
	
	const blockProps = useBlockProps( {
		className: `columns-${columns}`,
	} );
	
	// Build category options
	const categoryOptions = [
		{ label: __( 'All Categories', 'posts-grid-block' ), value: 0 }
	];
	
	if ( categories.length > 0 ) {
		categories.forEach( ( cat ) => {
			categoryOptions.push( {
				label: cat.name,
				value: cat.id,
			} );
		} );
	}
	
	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Posts Settings', 'posts-grid-block' ) }>
					<RangeControl
						label={ __( 'Number of posts', 'posts-grid-block' ) }
						value={ postsPerPage }
						onChange={ ( value ) => setAttributes( { postsPerPage: value } ) }
						min={ 1 }
						max={ 20 }
					/>
					
					<SelectControl
						label={ __( 'Order by', 'posts-grid-block' ) }
						value={ orderBy }
						options={ [
							{ label: __( 'Date', 'posts-grid-block' ), value: 'date' },
							{ label: __( 'Title', 'posts-grid-block' ), value: 'title' },
							{ label: __( 'Menu order', 'posts-grid-block' ), value: 'menu_order' },
						] }
						onChange={ ( value ) => setAttributes( { orderBy: value } ) }
					/>
					
					<SelectControl
						label={ __( 'Order', 'posts-grid-block' ) }
						value={ order }
						options={ [
							{ label: __( 'Descending', 'posts-grid-block' ), value: 'desc' },
							{ label: __( 'Ascending', 'posts-grid-block' ), value: 'asc' },
						] }
						onChange={ ( value ) => setAttributes( { order: value } ) }
					/>
					
					<SelectControl
						label={ __( 'Category', 'posts-grid-block' ) }
						value={ selectedCategory }
						options={ categoryOptions }
						onChange={ ( value ) => setAttributes( { selectedCategory: parseInt( value ) } ) }
					/>
				</PanelBody>
				
				<PanelBody title={ __( 'Layout Settings', 'posts-grid-block' ) }>
					<RangeControl
						label={ __( 'Columns', 'posts-grid-block' ) }
						value={ columns }
						onChange={ ( value ) => setAttributes( { columns: value } ) }
						min={ 1 }
						max={ 4 }
					/>
					
					<ToggleControl
						label={ __( 'Show date', 'posts-grid-block' ) }
						checked={ showDate }
						onChange={ ( value ) => setAttributes( { showDate: value } ) }
					/>
					
					<ToggleControl
						label={ __( 'Show excerpt', 'posts-grid-block' ) }
						checked={ showExcerpt }
						onChange={ ( value ) => setAttributes( { showExcerpt: value } ) }
					/>
				</PanelBody>
			</InspectorControls>
			
			<div { ...blockProps }>
				{ isLoading && (
					<Placeholder icon="grid-view" label={ __( 'Posts Grid', 'posts-grid-block' ) }>
						<Spinner />
					</Placeholder>
				) }
				
				{ ! isLoading && posts.length === 0 && (
					<Placeholder icon="grid-view" label={ __( 'Posts Grid', 'posts-grid-block' ) }>
						{ __( 'No posts found.', 'posts-grid-block' ) }
					</Placeholder>
				) }
				
				{ ! isLoading && posts.length > 0 && (
					<div className="wp-block-posts-grid">
						{ posts.map( ( post ) => (
							<article key={ post.id } className="wp-block-posts-grid__item">
								<h3 className="wp-block-posts-grid__title">
									<a href={ post.link }>{ post.title }</a>
								</h3>
								
								{ showDate && (
									<time className="wp-block-posts-grid__date">
										{ post.date }
									</time>
								) }
								
								{ showExcerpt && (
									<div 
										className="wp-block-posts-grid__excerpt"
										dangerouslySetInnerHTML={ { __html: post.excerpt } }
									/>
								) }
							</article>
						) ) }
					</div>
				) }
			</div>
		</>
	);
}
