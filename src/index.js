import { registerBlockType } from '@wordpress/blocks';
import { __ } from '@wordpress/i18n';
import { postList as icon } from '@wordpress/icons';
import Edit from './blocks/posts-grid/edit';
import './blocks/posts-grid/editor.scss';
import './blocks/posts-grid/style.scss';

registerBlockType( 'simple-posts-grid/posts-grid', {
	title: __( 'Posts Grid', 'simple-posts-grid' ),
	description: __( 'Display posts in a responsive grid layout', 'simple-posts-grid' ),
	category: 'widgets',
	icon,
	keywords: [ 
		__( 'posts', 'simple-posts-grid' ), 
		__( 'grid', 'simple-posts-grid' ), 
		__( 'blog', 'simple-posts-grid' ) 
	],
	supports: {
		align: [ 'wide', 'full' ],
		html: false,
	},
	attributes: {
		categories: {
			type: 'array',
			default: [],
		},
		postsPerPage: {
			type: 'number',
			default: 6,
		},
		columns: {
			type: 'number', 
			default: 3,
		},
		orderBy: {
			type: 'string',
			default: 'date',
		},
		order: {
			type: 'string',
			default: 'desc',
		},
		displayFeaturedImage: {
			type: 'boolean',
			default: true,
		},
		displayTitle: {
			type: 'boolean',
			default: true,
		},
		displayExcerpt: {
			type: 'boolean',
			default: true,
		},
		displayDate: {
			type: 'boolean',
			default: false,
		},
		displayAuthor: {
			type: 'boolean',
			default: false,
		},
	},
	edit: Edit,
	save: () => null, // Dynamic block
} );
