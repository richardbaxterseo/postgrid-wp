/**
 * PostGrid Block Registration
 * 
 * Registers the PostGrid block type with WordPress.
 * This is a dynamic block that renders on the server side.
 * 
 * @package PostGrid
 */

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
 * Register the PostGrid block
 * 
 * Uses block.json metadata for configuration and
 * provides the Edit component for the block editor.
 * Save returns null as this is a dynamic block.
 */
registerBlockType( metadata.name, {
	edit: Edit,
	save: () => null, // Dynamic block rendered server-side
} );
