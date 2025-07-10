/**
 * PostGrid Block with Social Icons - Integration Example
 * 
 * This shows how to add the social icons to your existing edit.js
 */

// Add this import at the top of your edit.js file
import '../social-icons/social-icons.css';

// Add these attributes to your block.json
const socialAttributes = {
    showSocialShare: {
        type: 'boolean',
        default: false
    },
    socialIconSize: {
        type: 'string',
        default: 'small'
    }
};

// Add this to your InspectorControls (inside the existing PanelBody or create a new one)
const socialControls = (
    <PanelBody title={ __( 'Social Sharing', 'postgrid' ) }>
        <ToggleControl
            label={ __( 'Show social sharing icons', 'postgrid' ) }
            checked={ showSocialShare }
            onChange={ ( value ) => setAttributes( { showSocialShare: value } ) }
        />
        
        { showSocialShare && (
            <SelectControl
                label={ __( 'Icon size', 'postgrid' ) }
                value={ socialIconSize }
                options={ [
                    { label: __( 'Small', 'postgrid' ), value: 'small' },
                    { label: __( 'Normal', 'postgrid' ), value: '' },
                    { label: __( 'Large', 'postgrid' ), value: 'large' },
                    { label: __( '2x', 'postgrid' ), value: '2x' },
                ] }
                onChange={ ( value ) => setAttributes( { socialIconSize: value } ) }
            />
        ) }
    </PanelBody>
);

// Add this component for rendering social icons
const SocialShareIcons = ({ post, size = 'small' }) => {
    const shareUrls = {
        facebook: `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(post.link)}`,
        twitter: `https://twitter.com/intent/tweet?url=${encodeURIComponent(post.link)}&text=${encodeURIComponent(post.title)}`,
        linkedin: `https://www.linkedin.com/sharing/share-offsite/?url=${encodeURIComponent(post.link)}`,
        pinterest: `https://pinterest.com/pin/create/button/?url=${encodeURIComponent(post.link)}&description=${encodeURIComponent(post.title)}`,
        email: `mailto:?subject=${encodeURIComponent(post.title)}&body=${encodeURIComponent(post.link)}`,
    };
    
    // SVG icons (you can import these from the social-icons.js file instead)
    const icons = {
        facebook: <svg viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>,
        twitter: <svg viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>,
        linkedin: <svg viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>,
        pinterest: <svg viewBox="0 0 24 24"><path d="M12.017 0C5.396 0 .029 5.367.029 11.987c0 5.079 3.158 9.417 7.618 11.162-.105-.949-.199-2.403.041-3.439.219-.937 1.406-5.957 1.406-5.957s-.359-.72-.359-1.781c0-1.663.967-2.911 2.168-2.911 1.024 0 1.518.769 1.518 1.688 0 1.029-.653 2.567-.992 3.992-.285 1.193.6 2.165 1.775 2.165 2.128 0 3.768-2.245 3.768-5.487 0-2.861-2.063-4.869-5.008-4.869-3.41 0-5.409 2.562-5.409 5.199 0 1.033.394 2.143.889 2.741.099.12.112.225.085.345-.09.375-.293 1.199-.334 1.363-.053.225-.172.271-.401.165-1.495-.69-2.433-2.878-2.433-4.646 0-3.776 2.748-7.252 7.92-7.252 4.158 0 7.392 2.967 7.392 6.923 0 4.135-2.607 7.462-6.233 7.462-1.214 0-2.354-.629-2.758-1.379l-.749 2.848c-.269 1.045-1.004 2.352-1.498 3.146 1.123.345 2.306.535 3.55.535 6.607 0 11.985-5.365 11.985-11.987C23.97 5.39 18.592.026 11.985.026L12.017 0z"/></svg>,
        email: <svg viewBox="0 0 24 24"><path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/></svg>,
    };
    
    return (
        <div className="postgrid-social-icons">
            { Object.keys( shareUrls ).map( ( platform ) => (
                <a 
                    key={ platform }
                    href={ shareUrls[ platform ] }
                    className={ `postgrid-social-icon postgrid-social-icon--${platform} postgrid-social-icon--${size}` }
                    target="_blank"
                    rel="noopener noreferrer"
                    aria-label={ `Share on ${platform}` }
                >
                    { icons[ platform ] }
                </a>
            ) ) }
        </div>
    );
};

// Update your post item rendering to include social icons
const renderPostItem = ( post ) => (
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
        
        { showSocialShare && (
            <SocialShareIcons post={ post } size={ socialIconSize } />
        ) }
    </article>
);
