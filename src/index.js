/**
 * WordPress dependencies
 */
import { registerBlockType } from '@wordpress/blocks';

/**
 * Internal dependencies
 */
import metadata from '../block.json';
import Edit from './edit';
import './style.css';

/**
 * Register the block
 */
registerBlockType( metadata.name, {
	edit: Edit,
	save: () => null, // Dynamic block
} );
