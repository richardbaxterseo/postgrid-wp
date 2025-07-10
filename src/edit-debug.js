/**
 * PostGrid Block Editor Component - Debug Version
 * 
 * This version includes console logging to debug the category dropdown issue
 * 
 * @package PostGrid
 */

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
 * Edit component for PostGrid block
 * 
 * @param {Object} props - Component props
 * @param {Object} props.attributes - Block attributes
 * @param {Function} props.setAttributes - Function to update attributes
 * @returns {JSX.Element} The edit component
 */
export default function Edit( { attributes, setAttributes } ) {
	console.log('PostGrid Edit - Attributes:', attributes);
	
	const { 
		postsPerPage, 
		orderBy, 
		order, 
		selectedCategory, 
		columns,
		showDate,
		showExcerpt 
	} = attributes;
	
	/**
	 * State for storing fetched posts
	 * @type {[Array, Function]}
	 */
	const [ posts, setPosts ] = useState( [] );
	
	/**
	 * State for tracking loading status
	 * @type {[boolean, Function]}
	 */
	const [ isLoading, setIsLoading ] = useState( false );
	
	/**
	 * Fetch categories from WordPress data store
	 * Uses useSelect hook to get all categories
	 */
	const categories = useSelect( ( select ) => {
		const { getEntityRecords } = select( 'core' );
		const cats = getEntityRecords( 'taxonomy', 'category', { per_page: -1 } );
		console.log('PostGrid - Fetched categories:', cats);
		return cats || [];
	}, [] );
	
	/**
	 * Effect hook to fetch posts when attributes change
	 * Triggers API call to PostGrid REST endpoint
	 */
	useEffect( () => {
		setIsLoading( true );
		
		const params = new URLSearchParams( {
			per_page: postsPerPage,
			orderby: orderBy,
			order: order,
			category: selectedCategory,
		} );
		
		console.log('PostGrid - Fetching posts with params:', params.toString());
		
		apiFetch( {
			path: `/postgrid/v1/posts?${params}`,
		} )
			.then( ( fetchedPosts ) => {
				console.log('PostGrid - Fetched posts:', fetchedPosts);
				setPosts( fetchedPosts );
				setIsLoading( false );
			} )
			.catch( (error) => {
				console.error('PostGrid - Error fetching posts:', error);
				setPosts( [] );
				setIsLoading( false );
			} );
	}, [ postsPerPage, orderBy, order, selectedCategory ] );
	
	const blockProps = useBlockProps( {
		className: `columns-${columns}`,
	} );
	
	// Build category options
	const categoryOptions = [
		{ label: __( 'All Categories', 'postgrid' ), value: 0 }
	];
	
	if ( categories.length > 0 ) {
		categories.forEach( ( cat ) => {
			categoryOptions.push( {
				label: cat.name,
				value: cat.id,
			} );
		} );
	}
	
	console.log('PostGrid - Category options:', categoryOptions);
	console.log('PostGrid - Selected category:', selectedCategory);
	
	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Posts Settings', 'postgrid' ) }>
					<RangeControl
						label={ __( 'Number of posts', 'postgrid' ) }
						value={ postsPerPage }
						onChange={ ( value ) => setAttributes( { postsPerPage: value } ) }
						min={ 1 }
						max={ 20 }
					/>
					
					<SelectControl
						label={ __( 'Order by', 'postgrid' ) }
						value={ orderBy }
						options={ [
							{ label: __( 'Date', 'postgrid' ), value: 'date' },
							{ label: __( 'Title', 'postgrid' ), value: 'title' },
							{ label: __( 'Menu order', 'postgrid' ), value: 'menu_order' },
						] }
						onChange={ ( value ) => setAttributes( { orderBy: value } ) }
					/>
					
					<SelectControl
						label={ __( 'Order', 'postgrid' ) }
						value={ order }
						options={ [
							{ label: __( 'Descending', 'postgrid' ), value: 'desc' },
							{ label: __( 'Ascending', 'postgrid' ), value: 'asc' },
						] }
						onChange={ ( value ) => setAttributes( { order: value } ) }
					/>
					
					<div style={{ backgroundColor: '#f0f0f0', padding: '10px', margin: '10px 0' }}>
						<p>DEBUG: Category dropdown should appear here:</p>
						<p>Categories loaded: {categories.length}</p>
						<p>Selected category: {selectedCategory}</p>
					</div>
					
					<SelectControl
						label={ __( 'Category', 'postgrid' ) }
						value={ selectedCategory }
						options={ categoryOptions }
						onChange={ ( value ) => {
							console.log('PostGrid - Category changed to:', value);
							setAttributes( { selectedCategory: parseInt( value ) } );
						} }
					/>
				</PanelBody>
				
				<PanelBody title={ __( 'Layout Settings', 'postgrid' ) }>
					<RangeControl
						label={ __( 'Columns', 'postgrid' ) }
						value={ columns }
						onChange={ ( value ) => setAttributes( { columns: value } ) }
						min={ 1 }
						max={ 4 }
					/>
					
					<ToggleControl
						label={ __( 'Show date', 'postgrid' ) }
						checked={ showDate }
						onChange={ ( value ) => setAttributes( { showDate: value } ) }
					/>
					
					<ToggleControl
						label={ __( 'Show excerpt', 'postgrid' ) }
						checked={ showExcerpt }
						onChange={ ( value ) => setAttributes( { showExcerpt: value } ) }
					/>
				</PanelBody>
			</InspectorControls>
			
			<div { ...blockProps }>
				{ isLoading && (
					<Placeholder icon="grid-view" label={ __( 'PostGrid', 'postgrid' ) }>
						<Spinner />
					</Placeholder>
				) }
				
				{ ! isLoading && posts.length === 0 && (
					<Placeholder icon="grid-view" label={ __( 'PostGrid', 'postgrid' ) }>
						{ __( 'No posts found.', 'postgrid' ) }
					</Placeholder>
				) }
				
				{ ! isLoading && posts.length > 0 && (
					<div className="wp-block-postgrid">
						{ posts.map( ( post ) => (
							<article key={ post.id } className="wp-block-postgrid__item">
								<h3 className="wp-block-postgrid__title">
									<a href={ post.link }>{ post.title }</a>
								</h3>
								
								{ showDate && (
									<time className="wp-block-postgrid__date">
										{ post.date }
									</time>
								) }
								
								{ showExcerpt && (
									<div 
										className="wp-block-postgrid__excerpt"
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