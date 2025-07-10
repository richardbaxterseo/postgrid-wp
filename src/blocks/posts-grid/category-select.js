import { __ } from '@wordpress/i18n';
import { Component } from '@wordpress/element';
import { CheckboxControl } from '@wordpress/components';

class CategorySelect extends Component {
	render() {
		const { categories, selectedCategories, onChange } = this.props;
		
		if ( ! categories ) {
			return <p>{ __( 'Loading categories...', 'simple-posts-grid' ) }</p>;
		}
		
		if ( categories.length === 0 ) {
			return <p>{ __( 'No categories found.', 'simple-posts-grid' ) }</p>;
		}
		
		const handleCategoryChange = ( categoryId, isChecked ) => {
			const updatedCategories = isChecked
				? [ ...selectedCategories, categoryId ]
				: selectedCategories.filter( id => id !== categoryId );
			
			onChange( updatedCategories );
		};
		
		return (
			<div className="spg-category-select">
				<label>{ __( 'Categories', 'simple-posts-grid' ) }</label>
				{ categories.map( category => (
					<CheckboxControl
						key={ category.id }
						label={ category.name }
						checked={ selectedCategories.includes( category.id ) }
						onChange={ ( isChecked ) => handleCategoryChange( category.id, isChecked ) }
					/>
				) ) }
			</div>
		);
	}
}

export default CategorySelect;
