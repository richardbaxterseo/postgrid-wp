# PostGrid Social Icons Library

A lightweight, inline SVG social icons solution for the PostGrid WordPress block. This replaces Font Awesome with a minimal implementation that adds less than 3KB to your plugin.

## Features

- **Ultra-lightweight**: Only ~2KB of CSS (compared to Font Awesome's 70KB+)
- **No external dependencies**: All icons are inline SVGs
- **Performance optimized**: No render-blocking resources
- **Fully customizable**: Easy size control and hover effects
- **Accessible**: Proper ARIA labels and semantic markup
- **Dark mode support**: Automatic color adjustments
- **Responsive**: Mobile-optimized with smaller sizes on small screens

## Included Icons

- Facebook
- Instagram
- Twitter/X
- LinkedIn
- YouTube
- Pinterest
- RSS
- Email
- Threads (new!)

## Installation

1. Copy the `social-icons` folder to your PostGrid plugin's `src` directory
2. Import the CSS in your main stylesheet or enqueue it separately:
   ```css
   @import 'social-icons/social-icons.css';
   ```
3. For React/Block Editor usage, import the components:
   ```javascript
   import { SocialIcon, SocialIconsGroup } from './social-icons/social-icons';
   ```

## Usage

### Basic HTML Implementation

```html
<div class="postgrid-social-icons">
    <a href="https://facebook.com/yourpage" 
       class="postgrid-social-icon postgrid-social-icon--facebook postgrid-social-icon--2x"
       target="_blank" 
       rel="noopener noreferrer"
       aria-label="Follow on Facebook">
        <svg viewBox="0 0 24 24">
            <!-- SVG path data -->
        </svg>
    </a>
</div>
```

### React Component Usage

```javascript
// Individual icon
<SocialIcon 
    icon="facebook" 
    url="https://facebook.com/yourpage" 
    size="2x" 
/>

// Group of icons
const socialLinks = [
    { icon: 'facebook', url: 'https://facebook.com/yourpage' },
    { icon: 'instagram', url: 'https://instagram.com/yourprofile' },
    { icon: 'threads', url: 'https://threads.net/yourprofile' },
];

<SocialIconsGroup icons={socialLinks} size="2x" />
```

### WordPress/PHP Implementation

```php
function render_postgrid_social_icons($size = '2x') {
    $social_links = array(
        'facebook' => 'https://facebook.com/yourpage',
        'instagram' => 'https://instagram.com/yourprofile',
        'pinterest' => 'https://pinterest.com/yourprofile',
        'youtube' => 'https://youtube.com/yourchannel',
        'linkedin' => 'https://linkedin.com/company/yourcompany',
        'threads' => 'https://threads.net/yourprofile',
        'rss' => '/feed',
        'email' => 'mailto:contact@example.com'
    );
    
    echo '<div class="postgrid-social-icons">';
    
    foreach ($social_links as $platform => $url) {
        printf(
            '<a href="%s" class="postgrid-social-icon postgrid-social-icon--%s postgrid-social-icon--%s" target="_blank" rel="noopener noreferrer" aria-label="Follow on %s">',
            esc_url($url),
            esc_attr($platform),
            esc_attr($size),
            ucfirst($platform)
        );
        
        // Include the SVG for each platform
        include_svg_icon($platform);
        
        echo '</a>';
    }
    
    echo '</div>';
}
```

## Size Options

The library includes four size presets:

- `small` - 1rem (16px)
- `(default)` - 1.25rem (20px)
- `large` - 1.5rem (24px)
- `2x` - 2rem (32px) - Matches Font Awesome's fa-2x

Apply sizes using the class modifier:
```html
<a class="postgrid-social-icon postgrid-social-icon--facebook postgrid-social-icon--small">
<a class="postgrid-social-icon postgrid-social-icon--facebook"> <!-- Default -->
<a class="postgrid-social-icon postgrid-social-icon--facebook postgrid-social-icon--large">
<a class="postgrid-social-icon postgrid-social-icon--facebook postgrid-social-icon--2x">
```

## Customization

### Custom Colors

Override the default hover colors in your theme:

```css
.postgrid-social-icon--facebook:hover {
    color: #3b5998; /* Custom Facebook blue */
}

.postgrid-social-icon--threads:hover {
    color: #101010; /* Custom Threads black */
}
```

### Custom Sizes

Add your own size variations:

```css
.postgrid-social-icon--3x svg {
    width: 3rem;
    height: 3rem;
}

.postgrid-social-icon--tiny svg {
    width: 0.75rem;
    height: 0.75rem;
}
```

### Custom Spacing

Adjust the gap between icons:

```css
.postgrid-social-icons {
    gap: 1rem; /* Default is 0.75rem */
}
```

## Integration with PostGrid

To add social sharing to each post in the grid:

1. Import the social icons CSS in your block's style.css:
   ```css
   @import 'social-icons/social-icons.css';
   ```

2. Modify your post item template to include sharing icons:
   ```javascript
   <article className="wp-block-postgrid__item">
       <h3 className="wp-block-postgrid__title">
           <a href={post.link}>{post.title}</a>
       </h3>
       
       {/* Existing content */}
       
       <div className="postgrid-social-icons">
           <SocialIcon 
               icon="facebook" 
               url={`https://www.facebook.com/sharer/sharer.php?u=${post.link}`} 
               size="small" 
           />
           {/* Add more sharing icons */}
       </div>
   </article>
   ```

## Performance Comparison

| Solution | Size | External Dependencies | Render Blocking |
|----------|------|----------------------|-----------------|
| Font Awesome | 70KB+ | Yes (CDN) | Yes |
| PostGrid Social Icons | ~2KB | No | No |

## Browser Support

- All modern browsers (Chrome, Firefox, Safari, Edge)
- IE11+ (with SVG support)
- Mobile browsers (iOS Safari, Chrome Android)

## Accessibility

All icons include:
- Proper ARIA labels
- Semantic HTML structure
- Keyboard navigation support
- Screen reader compatibility

## License

This social icons library is part of the PostGrid plugin and follows the same GPL v2+ license.

## Credits

SVG icons are optimized versions of popular social media brand icons, simplified for minimal file size while maintaining visual clarity.
